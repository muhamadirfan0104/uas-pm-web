<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';

    protected $fillable = ['pesanan_id', 'produk_id', 'user_id', 'rating', 'komentar', 'foto_ulasan', 'ditampilkan'];

    protected $casts = ['ditampilkan' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
