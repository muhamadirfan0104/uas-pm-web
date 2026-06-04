<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pembayaran::with(['pesanan.user'])->latest();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('referensi_pembayaran', 'like', '%' . $request->q . '%')
                    ->orWhereHas('pesanan', fn ($order) => $order->where('nomor_invoice', 'like', '%' . $request->q . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayaran = $query->paginate(12)->withQueryString();

        $stats = [
            'dibayar' => Pembayaran::where('status', 'dibayar')->count(),
            'menunggu' => Pembayaran::where('status', 'menunggu_pembayaran')->count(),
            'gagal' => Pembayaran::where('status', 'gagal')->count(),
            'kedaluwarsa' => Pembayaran::where('status', 'kedaluwarsa')->count(),
        ];

        return view('admin.pembayaran.index', compact('pembayaran', 'stats'));
    }

    public function updateStatus(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:menunggu_pembayaran,dibayar,gagal,kedaluwarsa,dibatalkan'],
        ]);

        $pembayaran->status = $data['status'];
        $pembayaran->dibayar_pada = $data['status'] === 'dibayar' ? Carbon::now() : null;
        $pembayaran->save();

        $pembayaran->pesanan?->update([
            'status_pembayaran' => $data['status'],
            'status' => $data['status'] === 'dibayar' ? 'dibayar' : $pembayaran->pesanan->status,
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
