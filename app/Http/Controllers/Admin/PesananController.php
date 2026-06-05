<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pesanan::with(['user', 'pembayaran', 'item.produk'])
            ->latest('tanggal_pesanan');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_invoice', 'like', '%' . $request->q . '%')
                    ->orWhereHas('user', function ($user) use ($request) {
                        $user->where('name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%')
                            ->orWhere('telepon', 'like', '%' . $request->q . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $pesanan = $query->paginate(12)->withQueryString();

        $statusCounts = Pesanan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.pesanan.index', compact('pesanan', 'statusCounts'));
    }

    public function show(Pesanan $pesanan): View
    {
        $pesanan->load([
            'user',
            'alamatPengiriman',
            'item.produk',
            'pembayaran',
            'pengiriman',
            'ulasan',
        ]);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function invoice(Pesanan $pesanan): View
    {
        $pesanan->load([
            'user',
            'alamatPengiriman',
            'item.produk',
            'pembayaran',
            'pengiriman',
        ]);

        return view('admin.pesanan.invoice', compact('pesanan'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:menunggu_pembayaran,dibayar,diproses,siap_diambil,dalam_pengantaran,selesai,dibatalkan'],
            'status_pembayaran' => ['nullable', 'in:menunggu_pembayaran,dibayar,gagal,kedaluwarsa,dibatalkan'],
        ]);

        $pesanan->status = $data['status'];

        if (isset($data['status_pembayaran'])) {
            $pesanan->status_pembayaran = $data['status_pembayaran'];

            if ($pesanan->pembayaran) {
                $pesanan->pembayaran->update([
                    'status' => $data['status_pembayaran'],
                    'dibayar_pada' => $data['status_pembayaran'] === 'dibayar'
                        ? now()
                        : $pesanan->pembayaran->dibayar_pada,
                ]);
            }
        }

        $pesanan->save();

        if ($pesanan->pengiriman) {
            $statusPengiriman = match ($pesanan->status) {
                'siap_diambil' => 'siap_diambil',
                'dalam_pengantaran' => 'dalam_pengantaran',
                'selesai' => 'selesai',
                default => $pesanan->pengiriman->status_pengiriman,
            };

            $pesanan->pengiriman->update([
                'status_pengiriman' => $statusPengiriman,
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}