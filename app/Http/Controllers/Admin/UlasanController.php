<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UlasanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $produkId = $request->query('produk_id', 'semua');
        $rating = $request->query('rating', 'semua');
        $status = $request->query('status', 'semua');
        $media = $request->query('media', 'semua');
        $sort = $request->query('sort', 'terbaru');

        if ($produkId !== 'semua' && ! ctype_digit((string) $produkId)) {
            $produkId = 'semua';
        }

        if (! in_array($rating, ['semua', '5', '4', '3', '2', '1'], true)) {
            $rating = 'semua';
        }

        if (! in_array($status, ['semua', 'tampil', 'sembunyi'], true)) {
            $status = 'semua';
        }

        if (! in_array($media, ['semua', 'foto', 'video', 'tanpa_media'], true)) {
            $media = 'semua';
        }

        if (! in_array($sort, ['terbaru', 'rating_tinggi', 'rating_rendah'], true)) {
            $sort = 'terbaru';
        }

        $query = Ulasan::query()
            ->with(['user', 'produk.gambarUtama', 'pesanan', 'media']);

        if ($produkId !== 'semua') {
            $query->where('produk_id', (int) $produkId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('komentar', 'like', '%' . $search . '%')
                    ->orWhereHas('produk', function ($produk) use ($search) {
                        $produk->where('nama', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('telepon', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('pesanan', function ($pesanan) use ($search) {
                        $pesanan->where('nomor_invoice', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($rating !== 'semua') {
            $query->where('rating', (int) $rating);
        }

        if ($status === 'tampil') {
            $query->where('ditampilkan', true);
        }

        if ($status === 'sembunyi') {
            $query->where('ditampilkan', false);
        }

        if ($media === 'foto') {
            $query->where(function ($q) {
                $q->whereNotNull('foto_ulasan')
                    ->orWhereHas('media', fn ($media) => $media->where('jenis', 'foto'));
            });
        }

        if ($media === 'video') {
            $query->where(function ($q) {
                $q->whereNotNull('video_ulasan')
                    ->orWhereHas('media', fn ($media) => $media->where('jenis', 'video'));
            });
        }

        if ($media === 'tanpa_media') {
            $query->whereNull('foto_ulasan')
                ->whereNull('video_ulasan')
                ->whereDoesntHave('media');
        }

        match ($sort) {
            'rating_tinggi' => $query->orderByDesc('rating')->latest(),
            'rating_rendah' => $query->orderBy('rating')->latest(),
            default => $query->latest(),
        };

        $ulasan = $query->paginate(18)->withQueryString();

        $base = Ulasan::query();
        $fotoCount = (clone $base)
            ->where(function ($query) {
                $query->whereNotNull('foto_ulasan')
                    ->orWhereHas('media', fn ($media) => $media->where('jenis', 'foto'));
            })
            ->count();

        $videoCount = (clone $base)
            ->where(function ($query) {
                $query->whereNotNull('video_ulasan')
                    ->orWhereHas('media', fn ($media) => $media->where('jenis', 'video'));
            })
            ->count();

        $stats = [
            'rata_rata' => round((float) Ulasan::avg('rating'), 1),
            'total' => Ulasan::count(),
            'tampil' => Ulasan::where('ditampilkan', true)->count(),
            'disembunyikan' => Ulasan::where('ditampilkan', false)->count(),
            'foto' => $fotoCount,
            'video' => $videoCount,
            'rating_rendah' => Ulasan::where('rating', '<=', 3)->count(),
        ];

        $produkList = Produk::query()
            ->whereHas('ulasan')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $ratingDistribusi = [];
        for ($bintang = 5; $bintang >= 1; $bintang--) {
            $ratingDistribusi[$bintang] = Ulasan::where('rating', $bintang)->count();
        }

        return view('admin.ulasan.index', compact(
            'ulasan',
            'stats',
            'ratingDistribusi',
            'produkList',
            'produkId',
            'search',
            'rating',
            'status',
            'media',
            'sort'
        ));
    }

    public function toggle(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->update([
            'ditampilkan' => ! $ulasan->ditampilkan,
        ]);

        return back()->with('success', $ulasan->ditampilkan ? 'Ulasan ditampilkan di halaman pembeli.' : 'Ulasan disembunyikan dari halaman pembeli.');
    }

    public function destroy(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
