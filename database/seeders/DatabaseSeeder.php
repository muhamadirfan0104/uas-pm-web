<?php

namespace Database\Seeders;

use App\Models\Alamat;
use App\Models\ItemPesanan;
use App\Models\PengaturanToko;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@gmail.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin TahuKu'),
                'telepon' => env('ADMIN_PHONE', '081234567890'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role' => 'admin',
                'aktif' => true,
            ]
        );

        $pembeli = User::updateOrCreate(
            ['email' => env('PEMBELI_EMAIL', 'pembeli@gmail.com')],
            [
                'name' => env('PEMBELI_NAME', 'Pembeli Demo'),
                'telepon' => env('PEMBELI_PHONE', '081234567892'),
                'password' => Hash::make(env('PEMBELI_PASSWORD', 'password')),
                'role' => 'pembeli',
                'aktif' => true,
            ]
        );

        PengaturanToko::query()->updateOrCreate(['id' => 1], [
            'nama' => env('TOKO_NAMA', 'SiTahu Premium'),
            'telepon' => env('TOKO_TELEPON', '081234567890'),
            'email' => env('TOKO_EMAIL', 'admin@sitahu.test'),
            'alamat' => env('TOKO_ALAMAT', 'Alamat toko belum diisi'),
            'latitude_toko' => -7.848016,
            'longitude_toko' => 112.017829,
            'jam_buka' => '07:00',
            'jam_tutup' => '20:00',
            'tarif_per_km' => 3000,
            'biaya_minimum_pengiriman' => 8000,
            'radius_maksimal_km' => 5,
            'info_pembayaran' => 'Transfer pembayaran ke rekening toko, lalu unggah bukti transfer saat checkout. COD tersedia untuk ambil di toko atau kurir toko.',
            'bank_nama' => 'BCA',
            'bank_nomor_rekening' => '1234567890',
            'bank_atas_nama' => 'SiTahu Premium',
            'tentang' => 'SiTahu menyediakan produk tahu segar berkualitas untuk kebutuhan rumah tangga, usaha kuliner, dan acara keluarga.',
        ]);


        $alamatUtama = Alamat::updateOrCreate(
            [
                'user_id' => $pembeli->id,
                'nama_penerima' => 'Pembeli Demo',
                'telepon' => '081234567892',
            ],
            [
                'email_penerima' => 'pembeli@gmail.com',
                'alamat_lengkap' => 'Jl. Melati No. 12, RT 03/RW 02, Kecamatan Sukamaju, Kota Demo',
                'latitude' => -7.850300,
                'longitude' => 112.020100,
                'utama' => true,
            ]
        );

        Alamat::updateOrCreate(
            [
                'user_id' => $pembeli->id,
                'nama_penerima' => 'Ibu Sari',
                'telepon' => '082233445566',
            ],
            [
                'email_penerima' => 'sari@example.com',
                'alamat_lengkap' => 'Jl. Mawar No. 8, dekat Pasar Pagi, Kecamatan Sukamaju, Kota Demo',
                'latitude' => -7.842900,
                'longitude' => 112.025700,
                'utama' => false,
            ]
        );

        $produkDemo = collect([
            [
                'nama' => 'Tahu Putih Segar',
                'harga' => 12000,
                'stok' => 80,
                'min_stok' => 10,
                'satuan' => 'Pack',
                'isi_per_satuan' => 10,
                'berat' => 500,
                'deskripsi' => 'Tahu putih segar dengan tekstur lembut, cocok untuk digoreng, ditumis, atau dimasak sayur.',
            ],
            [
                'nama' => 'Tahu Kuning Gurih',
                'harga' => 14000,
                'stok' => 65,
                'min_stok' => 10,
                'satuan' => 'Pack',
                'isi_per_satuan' => 10,
                'berat' => 500,
                'deskripsi' => 'Tahu kuning gurih dengan warna menarik dan rasa khas, cocok untuk lauk harian.',
            ],
            [
                'nama' => 'Paket Tahu Hemat Keluarga',
                'harga' => 25000,
                'stok' => 40,
                'min_stok' => 8,
                'satuan' => 'Paket',
                'isi_per_satuan' => 20,
                'berat' => 1000,
                'deskripsi' => 'Paket hemat tahu segar untuk kebutuhan keluarga atau stok usaha makanan kecil.',
            ],
        ])->map(function (array $item) {
            return Produk::updateOrCreate(
                ['nama' => $item['nama']],
                $item + [
                    'masa_simpan' => 2,
                    'saran_penyimpanan' => 'Simpan di chiller agar kualitas tetap terjaga.',
                    'saran_penyajian' => 'Goreng dengan minyak panas atau olah sesuai menu favorit.',
                    'aktif' => true,
                ]
            );
        });

        $subtotal = $produkDemo->sum(fn (Produk $produk) => (float) $produk->harga * 2);

        $pesanan = Pesanan::updateOrCreate(
            ['nomor_invoice' => 'INV-DEMO-PEMBELI-001'],
            [
                'user_id' => $pembeli->id,
                'tanggal_pesanan' => now()->subDays(3),
                'subtotal_produk' => $subtotal,
                'jarak_km' => 0,
                'biaya_pengiriman' => 0,
                'total_bayar' => $subtotal,
                'metode_pengambilan' => 'ambil_toko',
                'alamat_pengiriman_id' => $alamatUtama->id,
                'status' => 'selesai',
                'status_pembayaran' => 'dibayar',
            ]
        );

        foreach ($produkDemo as $produk) {
            ItemPesanan::updateOrCreate(
                [
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $produk->id,
                ],
                [
                    'jumlah' => 2,
                    'harga_satuan' => $produk->harga,
                    'subtotal' => (float) $produk->harga * 2,
                ]
            );
        }

        foreach ($produkDemo as $index => $produk) {
            Ulasan::updateOrCreate(
                [
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $produk->id,
                    'user_id' => $pembeli->id,
                ],
                [
                    'rating' => [5, 5, 4][$index] ?? 5,
                    'komentar' => [
                        'Produknya segar, teksturnya lembut, dan cocok untuk lauk keluarga.',
                        'Tahu kuningnya gurih dan kualitasnya konsisten.',
                        'Paket hematnya pas untuk stok di rumah, harga juga masuk akal.',
                    ][$index] ?? 'Produk bagus dan pelayanan toko responsif.',
                    'foto_ulasan' => null,
                    'video_ulasan' => null,
                    'ditampilkan' => true,
                ]
            );
        }
    }
}
