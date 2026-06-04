<?php

namespace Database\Seeders;

use App\Models\PengaturanToko;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@gmail.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin TahuKu'),
                'telepon' => env('ADMIN_PHONE', '081234567890'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role' => 'admin',
                'aktif' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => env('KASIR_EMAIL', 'kasir@gmail.com')],
            [
                'name' => env('KASIR_NAME', 'Kasir TahuKu'),
                'telepon' => env('KASIR_PHONE', '081234567891'),
                'password' => Hash::make(env('KASIR_PASSWORD', 'password')),
                'role' => 'kasir',
                'aktif' => true,
            ]
        );

        PengaturanToko::query()->firstOrCreate(['id' => 1], [
            'nama' => env('TOKO_NAMA', 'TahuKu'),
            'telepon' => env('TOKO_TELEPON', '081234567890'),
            'email' => env('TOKO_EMAIL', 'admin@tahuku.test'),
            'alamat' => env('TOKO_ALAMAT', 'Alamat toko belum diisi'),
            'jam_buka' => '07:00',
            'jam_tutup' => '20:00',
            'biaya_minimum_pengiriman' => 8000,
            'radius_maksimal_km' => 5,
            'tentang' => 'Toko tahu segar dengan produk tahu pilihan.',
        ]);
    }
}
