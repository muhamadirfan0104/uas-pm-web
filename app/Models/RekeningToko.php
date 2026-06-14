<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekeningToko extends Model
{
    use SoftDeletes;

    protected $table = 'rekening_toko';

    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'aktif',
        'utama',
        'urutan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'utama' => 'boolean',
        'urutan' => 'integer',
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public static function daftarAktif()
    {
        return static::query()
            ->aktif()
            ->orderByDesc('utama')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();
    }
}
