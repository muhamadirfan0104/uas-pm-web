<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanToko extends Model
{
    protected $table = 'pengaturan_toko';

    protected $fillable = [
        'nama', 'logo_url', 'alamat', 'telepon', 'email', 'jam_buka', 'jam_tutup',
        'latitude_toko', 'longitude_toko', 'tarif_per_km', 'biaya_minimum_pengiriman',
        'radius_maksimal_km', 'area_pengiriman', 'info_pembayaran', 'tentang',
    ];

    protected $casts = [
        'latitude_toko' => 'decimal:7',
        'longitude_toko' => 'decimal:7',
        'tarif_per_km' => 'decimal:2',
        'biaya_minimum_pengiriman' => 'decimal:2',
        'radius_maksimal_km' => 'decimal:2',
    ];

    public static function utama(): self
    {
        return self::query()->firstOrCreate(['id' => 1], [
            'nama' => 'TahuKu',
            'email' => 'admin@tahuku.test',
            'telepon' => '081234567890',
        ]);
    }
}
