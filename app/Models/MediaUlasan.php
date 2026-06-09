<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaUlasan extends Model
{
    protected $table = 'media_ulasan';

    protected $fillable = [
        'ulasan_id',
        'jenis',
        'path',
        'caption',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function ulasan()
    {
        return $this->belongsTo(Ulasan::class, 'ulasan_id');
    }
}
