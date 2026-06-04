<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\RiwayatStok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StokController extends Controller
{
    public function index(Request $request): View
    {
        $produkQuery = Produk::query()->latest();

        if ($request->filled('q')) {
            $produkQuery->where('nama', 'like', '%' . $request->q . '%');
        }

        if ($request->filter === 'menipis') {
            $produkQuery->where('stok', '>', 0)->whereColumn('stok', '<=', 'min_stok');
        } elseif ($request->filter === 'habis') {
            $produkQuery->where('stok', '<=', 0);
        }

        $produk = $produkQuery->paginate(10)->withQueryString();
        $riwayat = RiwayatStok::with('produk')->latest()->limit(250)->get();

        $stats = [
            'total' => Produk::count(),
            'aman' => Produk::whereColumn('stok', '>', 'min_stok')->count(),
            'menipis' => Produk::where('stok', '>', 0)->whereColumn('stok', '<=', 'min_stok')->count(),
            'habis' => Produk::where('stok', '<=', 0)->count(),
        ];

        return view('admin.stok.index', compact('produk', 'riwayat', 'stats'));
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $request->validate([
            'tipe' => ['required', 'in:tambah,kurang,penyesuaian'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        $stokAwal = $produk->stok;

        if ($data['tipe'] === 'tambah') {
            $produk->stok += $data['jumlah'];
            $perubahan = $data['jumlah'];
        } elseif ($data['tipe'] === 'kurang') {
            $produk->stok = max(0, $produk->stok - $data['jumlah']);
            $perubahan = -min($stokAwal, $data['jumlah']);
        } else {
            $produk->stok = $data['jumlah'];
            $perubahan = $produk->stok - $stokAwal;
        }

        $produk->save();

        RiwayatStok::create([
            'produk_id' => $produk->id,
            'perubahan' => $perubahan,
            'tipe' => $data['tipe'],
            'catatan' => $data['catatan'] ?? null,
        ]);

        return back()->with('success', 'Stok berhasil diperbarui.');
    }
}
