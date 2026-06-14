<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alamat extends Model
{
    use SoftDeletes;

    protected $table = 'alamat';

    protected $fillable = [
        'user_id',
        'nama_penerima',
        'telepon',
        'email_penerima',
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
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}