<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembeliController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'semua');
        $sort = $request->query('sort', 'terbaru');

        if (! in_array($status, ['semua', 'aktif', 'nonaktif'], true)) {
            $status = 'semua';
        }


        if (! in_array($sort, ['terbaru', 'nama', 'belanja_terbesar', 'pesanan_terbanyak', 'terakhir_belanja'], true)) {
            $sort = 'terbaru';
        }

        $query = User::query()
            ->where('role', 'pembeli')
            ->withCount(['pesanan', 'ulasan', 'alamat'])
            ->addSelect([
                'total_belanja' => Pesanan::query()
                    ->selectRaw('COALESCE(SUM(total_bayar), 0)')
                    ->whereColumn('pesanan.user_id', 'users.id')
                    ->where('status_pembayaran', 'dibayar'),
                'terakhir_belanja' => Pesanan::query()
                    ->select('tanggal_pesanan')
                    ->whereColumn('pesanan.user_id', 'users.id')
                    ->latest('tanggal_pesanan')
                    ->limit(1),
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('telepon', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'aktif') {
            $query->where('aktif', true);
        }

        if ($status === 'nonaktif') {
            $query->where('aktif', false);
        }


        match ($sort) {
            'nama' => $query->orderBy('name'),
            'belanja_terbesar' => $query->orderByDesc('total_belanja'),
            'pesanan_terbanyak' => $query->orderByDesc('pesanan_count'),
            'terakhir_belanja' => $query->orderByDesc('terakhir_belanja'),
            default => $query->latest(),
        };

        $pembeli = $query->paginate(12)->withQueryString();

        $base = User::query()->where('role', 'pembeli');

        $stats = [
            'total' => (clone $base)->count(),
            'aktif' => (clone $base)->where('aktif', true)->count(),
            'nonaktif' => (clone $base)->where('aktif', false)->count(),
            'pernah_belanja' => (clone $base)->whereHas('pesanan')->count(),
            'pesanan_aktif' => (clone $base)->whereHas('pesanan', function ($pesanan) {
                $pesanan->whereNotIn('status', ['selesai', 'dibatalkan']);
            })->count(),
            'total_belanja' => Pesanan::query()
                ->where('status_pembayaran', 'dibayar')
                ->sum('total_bayar'),
        ];

        return view('admin.pembeli.index', compact(
            'pembeli',
            'stats',
            'search',
            'status',
            'sort'
        ));
    }

    public function show(User $pembeli): View
    {
        abort_unless($pembeli->role === 'pembeli', 404);

        $pembeli->load([
            'alamat' => function ($query) {
                $query->latest();
            },
            'pesanan' => function ($query) {
                $query->with(['pembayaran', 'pengiriman', 'item.produk.gambarUtama'])
                    ->latest('tanggal_pesanan')
                    ->take(25);
            },
            'ulasan.produk.gambarUtama',
            'ulasan.media',
        ]);

        $totalPesanan = $pembeli->pesanan()->count();

        $totalBelanja = $pembeli->pesanan()
            ->where('status_pembayaran', 'dibayar')
            ->sum('total_bayar');

        $pesananSelesai = $pembeli->pesanan()
            ->where('status', 'selesai')
            ->count();

        $pesananAktif = $pembeli->pesanan()
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->count();

        $totalUlasan = $pembeli->ulasan()->count();

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
