<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'user_id', 'nomor_invoice', 'tanggal_pesanan', 'subtotal_produk', 'jarak_km',
        'biaya_pengiriman', 'total_bayar', 'metode_pengambilan', 'alamat_pengiriman_id',
        'status', 'status_pembayaran',
    ];

    protected $casts = [
        'tanggal_pesanan' => 'datetime',
        'subtotal_produk' => 'decimal:2',
        'jarak_km' => 'decimal:2',
        'biaya_pengiriman' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alamatPengiriman()
    {
        return $this->belongsTo(Alamat::class, 'alamat_pengiriman_id');
    }

    public function item()
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id');
    }

        public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'pesanan_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'pesanan_id');
    }
}
