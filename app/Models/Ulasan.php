<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ulasan extends Model
{
    use SoftDeletes;

    protected $table = 'ulasan';

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'user_id',
        'rating',
        'komentar',
        'foto_ulasan',
        'video_ulasan',
        'ditampilkan',
    ];

    protected $casts = [
        'ditampilkan' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->withTrashed();
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id')->withTrashed();
    }

    public function media()
    {
        return $this->hasMany(MediaUlasan::class, 'ulasan_id')->orderBy('urutan')->latest('id');
    }

}