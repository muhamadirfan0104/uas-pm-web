<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemKeranjang extends Model
{
    use SoftDeletes;

    protected $table = 'item_keranjang';

    protected $fillable = [
        'keranjang_id',
        'produk_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'keranjang_id')->withTrashed();
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->withTrashed();
    }
}
