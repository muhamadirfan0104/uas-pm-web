<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembeliController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('role', 'pembeli')
            ->withCount(['pesanan', 'ulasan'])
            ->latest();

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('telepon', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('aktif', true);
            }

            if ($request->status === 'nonaktif') {
                $query->where('aktif', false);
            }
        }

        $pembeli = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => User::query()
                ->where('role', 'pembeli')
                ->count(),

            'aktif' => User::query()
                ->where('role', 'pembeli')
                ->where('aktif', true)
                ->count(),

            'nonaktif' => User::query()
                ->where('role', 'pembeli')
                ->where('aktif', false)
                ->count(),
        ];

        return view('admin.pembeli.index', compact('pembeli', 'stats'));
    }

    public function show(User $pembeli): View
    {
        abort_unless($pembeli->role === 'pembeli', 404);

        $pembeli->load([
            'alamat' => function ($query) {
                $query->latest();
            },
            'pesanan' => function ($query) {
                $query->with(['pembayaran', 'pengiriman', 'item.produk'])
                    ->latest('tanggal_pesanan');
            },
            'ulasan.produk',
        ]);

        $totalPesanan = $pembeli->pesanan->count();

        $totalBelanja = $pembeli->pesanan
            ->where('status_pembayaran', 'dibayar')
            ->sum('total_bayar');

        $pesananSelesai = $pembeli->pesanan
            ->where('status', 'selesai')
            ->count();

        $pesananAktif = $pembeli->pesanan
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->count();

        $totalUlasan = $pembeli->ulasan->count();

        $alamatUtama = $pembeli->alamat
            ->firstWhere('utama', true);

        return view('admin.pembeli.show', compact(
            'pembeli',
            'totalPesanan',
            'totalBelanja',
            'pesananSelesai',
            'pesananAktif',
            'totalUlasan',
            'alamatUtama'
        ));
    }

    public function toggle(User $pembeli): RedirectResponse
    {
        abort_unless($pembeli->role === 'pembeli', 404);

        $pembeli->update([
            'aktif' => ! $pembeli->aktif,
        ]);

        return back()->with('success', 'Status akun pembeli berhasil diubah.');
    }
}