<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id', 'metode_pembayaran', 'referensi_pembayaran', 'jumlah',
        'status', 'tautan_pembayaran', 'qr_code', 'bukti_transfer', 'catatan_admin', 'dibayar_pada', 'diverifikasi_pada',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'dibayar_pada' => 'datetime',
        'diverifikasi_pada' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
