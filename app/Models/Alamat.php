<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $table = 'alamat';

    protected $fillable = [
        'user_id', 'nama_penerima', 'telepon', 'alamat_lengkap',
        'latitude', 'longitude', 'utama',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'utama' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
