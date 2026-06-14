<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaUlasan extends Model
{
    use SoftDeletes;

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
        return $this->belongsTo(Ulasan::class, 'ulasan_id')->withTrashed();
    }
}
