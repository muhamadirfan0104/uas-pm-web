<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengiriman extends Model
{
    use SoftDeletes;

    protected $table = 'pengiriman';

    protected $fillable = [
        'pesanan_id', 'metode', 'status_pengiriman', 'alamat_toko', 'alamat_tujuan',
        'latitude_tujuan', 'longitude_tujuan', 'jarak_km', 'biaya',
    ];

    protected $casts = [
        'latitude_tujuan' => 'decimal:7',
        'longitude_tujuan' => 'decimal:7',
        'jarak_km' => 'decimal:2',
        'biaya' => 'decimal:2',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id')->withTrashed();
    }
}
