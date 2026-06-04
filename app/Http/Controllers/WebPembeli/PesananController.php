<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('keyword'));

        $pesananList = collect();

        if ($keyword !== '') {
            $pesananList = Pesanan::query()
                ->with(['user', 'item.produk', 'pembayaran', 'pengiriman'])
                ->where(function ($query) use ($keyword) {
                    $query->where('nomor_invoice', 'like', '%' . $keyword . '%')
                        ->orWhereHas('user', function ($userQuery) use ($keyword) {
                            $userQuery->where('email', 'like', '%' . $keyword . '%')
                                ->orWhere('telepon', 'like', '%' . $keyword . '%')
                                ->orWhere('name', 'like', '%' . $keyword . '%');
                        });
                })
                ->latest()
                ->take(10)
                ->get();
        }

        return view('pembeli.pesanan', compact(
            'keyword',
            'pesananList'
        ));
    }

    public function show(string $nomor_invoice): View
    {
        $pesanan = Pesanan::query()
            ->with([
                'user',
                'item.produk.gambarUtama',
                'pembayaran',
                'pengiriman',
                'alamatPengiriman',
            ])
            ->where('nomor_invoice', $nomor_invoice)
            ->firstOrFail();

        return view('pembeli.detail-pesanan', compact('pesanan'));
    }
}
