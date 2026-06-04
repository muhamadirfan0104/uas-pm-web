<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPesanan extends Model
{
    protected $table = 'item_pesanan';

    protected $fillable = ['pesanan_id', 'produk_id', 'jumlah', 'harga_satuan', 'subtotal'];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
