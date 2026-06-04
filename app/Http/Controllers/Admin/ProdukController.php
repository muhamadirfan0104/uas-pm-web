<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GambarProduk;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(Request $request): View
    {
        $query = Produk::with('gambarUtama')->latest();

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('aktif', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('aktif', false);
            } elseif ($request->status === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        if ($request->sort === 'harga_terendah') {
            $query->orderBy('harga');
        } elseif ($request->sort === 'harga_tertinggi') {
            $query->orderByDesc('harga');
        } elseif ($request->sort === 'stok_terendah') {
            $query->orderBy('stok');
        }

        $produk = $query->paginate(10)->withQueryString();

        return view('admin.produk.index', compact('produk'));
    }

    public function create(): View
    {
        return view('admin.produk.create', ['produk' => new Produk()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $produk = Produk::create($this->validated($request));
        $this->simpanGambar($request, $produk);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk): View
    {
        $produk->load('gambarUtama');
        return view('admin.produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $produk->update($this->validated($request));
        $this->simpanGambar($request, $produk, true);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        foreach ($produk->gambar as $gambar) {
            Storage::disk('public')->delete($gambar->url_gambar);
        }
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function toggle(Produk $produk): RedirectResponse
    {
        $produk->update(['aktif' => ! $produk->aktif]);

        return back()->with('success', 'Status produk berhasil diubah.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok' => ['nullable', 'integer', 'min:0'],
            'min_stok' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'isi_per_satuan' => ['nullable', 'integer', 'min:0'],
            'berat' => ['nullable', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'masa_simpan' => ['nullable', 'integer', 'min:0'],
            'saran_penyimpanan' => ['nullable', 'string', 'max:255'],
            'saran_penyajian' => ['nullable', 'string', 'max:255'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => false];

        if (! $request->has('stok')) {
            unset($data['stok']);
        }

        return $data;
    }

    private function simpanGambar(Request $request, Produk $produk, bool $hapusLama = false): void
    {
        $request->validate([
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if (! $request->hasFile('foto')) {
            return;
        }

        if ($hapusLama) {
            foreach ($produk->gambar as $gambar) {
                Storage::disk('public')->delete($gambar->url_gambar);
                $gambar->delete();
            }
        }

        $path = $request->file('foto')->store('produk', 'public');

        GambarProduk::create([
            'produk_id' => $produk->id,
            'url_gambar' => $path,
            'utama' => true,
        ]);
    }
}
