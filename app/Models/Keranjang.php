<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keranjang extends Model
{
    use SoftDeletes;

    protected $table = 'keranjang';

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function item()
    {
        return $this->hasMany(ItemKeranjang::class, 'keranjang_id');
    }
}
