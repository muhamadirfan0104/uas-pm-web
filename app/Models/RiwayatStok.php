<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatStok extends Model
{
    use SoftDeletes;

    protected $table = 'riwayat_stok';

    protected $fillable = ['produk_id', 'perubahan', 'tipe', 'catatan'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->withTrashed();
    }
}
