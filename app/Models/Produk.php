<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama', 'harga', 'stok', 'min_stok', 'satuan', 'isi_per_satuan', 'berat',
        'deskripsi', 'masa_simpan', 'saran_penyimpanan', 'saran_penyajian', 'aktif',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'berat' => 'decimal:2',
        'aktif' => 'boolean',
        'min_stok' => 'integer',
    ];

    public function gambar()
    {
        return $this->hasMany(GambarProduk::class, 'produk_id');
    }

    public function gambarUtama()
    {
        return $this->hasOne(GambarProduk::class, 'produk_id')->where('utama', true);
    }

    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'produk_id');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'produk_id')->latest();
    }
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'produk_id');
    }
}
