<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GambarProduk extends Model
{
    use SoftDeletes;

    protected $table = 'gambar_produk';

    protected $fillable = ['produk_id', 'url_gambar', 'utama'];

    protected $casts = ['utama' => 'boolean'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->withTrashed();
    }
}
