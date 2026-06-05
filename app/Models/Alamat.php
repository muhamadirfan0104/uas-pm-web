<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $table = 'alamat';

    protected $fillable = [
        'user_id',
        'nama_penerima',
        'telepon',
        'alamat_lengkap',
        'latitude',
        'longitude',
        'utama',
    ];

    protected $casts = [
        'utama' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}