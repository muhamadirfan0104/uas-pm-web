<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanToko extends Model
{
    protected $table = 'pengaturan_toko';

    protected $fillable = [
        'nama',
        'logo_url',
        'alamat',
        'telepon',
        'email',
        'jam_buka',
        'jam_tutup',
        'latitude_toko',
        'longitude_toko',
        'tarif_per_km',
        'biaya_minimum_pengiriman',
        'radius_maksimal_km',
        'area_pengiriman',
        'info_pembayaran',
        'bank_nama',
        'bank_nomor_rekening',
        'bank_atas_nama',
        'tentang',
    ];

    protected $casts = [
        'latitude_toko' => 'decimal:8',
        'longitude_toko' => 'decimal:8',
        'tarif_per_km' => 'decimal:2',
        'biaya_minimum_pengiriman' => 'decimal:2',
        'radius_maksimal_km' => 'decimal:2',
    ];

    public static function utama(): self
    {
        return static::query()->firstOrCreate(
            [],
            [
                'nama' => 'SiTahu',
                'alamat' => 'Kediri',
                'telepon' => '081234567890',
                'email' => 'sitahu@example.com',
                'jam_buka' => '08.00 - 17.00 WIB',
                'latitude_toko' => null,
                'longitude_toko' => null,
                'tarif_per_km' => 3000,
                'biaya_minimum_pengiriman' => 5000,
                'radius_maksimal_km' => 0,
                'area_pengiriman' => 'Area sekitar toko',
                'info_pembayaran' => 'Transfer bank diverifikasi admin setelah bukti pembayaran dikirim.',
                'bank_nama' => 'BCA',
                'bank_nomor_rekening' => '1234567890',
                'bank_atas_nama' => 'SiTahu Premium',
                'tentang' => 'SiTahu menyediakan produk tahu segar untuk pembeli.',
            ]
        );
    }
}