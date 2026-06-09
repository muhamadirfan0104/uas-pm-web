<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        return response()->json([
            'success' => true,
            'data' => [
                'total_penjualan' => Pembayaran::where('status', 'dibayar')->sum('jumlah'),
                'total_pesanan' => Pesanan::count(),
                'pesanan_diproses' => Pesanan::where('status', 'diproses')->count(),
                'total_pembeli' => User::where('role', 'pembeli')->count(),
                'produk_aktif' => Produk::where('aktif', true)->count(),
                'stok_menipis' => Produk::where('stok', '>', 0)->where('stok', '<=', 20)->count(),
                'stok_habis' => Produk::where('stok', '<=', 0)->count(),
            ],
        ]);
    }
}
