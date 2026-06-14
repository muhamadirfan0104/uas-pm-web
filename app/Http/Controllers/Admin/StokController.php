<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\RiwayatStok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StokController extends Controller
{
    public function index(Request $request): View
    {
        $produkQuery = Produk::query()->with('gambarUtama');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $produkQuery->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('satuan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filter === 'aman') {
            $produkQuery->whereColumn('stok', '>', 'min_stok');
        } elseif ($request->filter === 'menipis') {
            $produkQuery->where('stok', '>', 0)->whereColumn('stok', '<=', 'min_stok');
        } elseif ($request->filter === 'habis') {
            $produkQuery->where('stok', '<=', 0);
        }

        match ($request->sort) {
            'stok_terendah' => $produkQuery->orderBy('stok'),
            'stok_terbanyak' => $produkQuery->orderByDesc('stok'),
            'nama' => $produkQuery->orderBy('nama'),
            default => $produkQuery->latest(),
        };

        $produk = $produkQuery->paginate(10)->withQueryString();

        $riwayatQuery = RiwayatStok::query()->with('produk');

        if ($request->filled('riwayat_q')) {
            $keyword = trim((string) $request->riwayat_q);
            $riwayatQuery->whereHas('produk', function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('tipe')) {
            $riwayatQuery->where('tipe', $request->tipe);
        }

        if ($request->filled('tanggal_mulai')) {
            $riwayatQuery->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $riwayatQuery->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $riwayat = $riwayatQuery->latest()->paginate(12, ['*'], 'riwayat_page')->withQueryString();

        $stats = [
            'total' => Produk::count(),
            'aman' => Produk::whereColumn('stok', '>', 'min_stok')->count(),
            'menipis' => Produk::where('stok', '>', 0)->whereColumn('stok', '<=', 'min_stok')->count(),
            'habis' => Produk::where('stok', '<=', 0)->count(),
            'total_stok' => Produk::sum('stok'),
            'pergerakan_hari_ini' => RiwayatStok::whereDate('created_at', today())->count(),
        ];

        return view('admin.stok.index', compact('produk', 'riwayat', 'stats'));
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $request->validate([
            'tipe' => ['required', 'in:tambah,kurang,penyesuaian'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($produk, $data) {
            $produkTerkunci = Produk::query()
                ->whereKey($produk->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stokAwal = max(0, (int) $produkTerkunci->stok);

            if ($data['tipe'] === 'tambah') {
                $stokBaru = $stokAwal + (int) $data['jumlah'];
                $perubahan = (int) $data['jumlah'];
            } elseif ($data['tipe'] === 'kurang') {
                $stokBaru = max(0, $stokAwal - (int) $data['jumlah']);
                $perubahan = -min($stokAwal, (int) $data['jumlah']);
            } else {
                $stokBaru = max(0, (int) $data['jumlah']);
                $perubahan = $stokBaru - $stokAwal;
            }

            $produkTerkunci->update([
                'stok' => $stokBaru,
            ]);

            RiwayatStok::create([
                'produk_id' => $produkTerkunci->id,
                'perubahan' => $perubahan,
                'tipe' => $data['tipe'],
                'catatan' => $data['catatan'] ?? null,
            ]);
        });

        return back()->with('success', 'Stok berhasil diperbarui.');
    }
}
