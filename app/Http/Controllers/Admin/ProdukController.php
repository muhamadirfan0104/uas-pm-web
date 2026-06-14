<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GambarProduk;
use App\Models\ItemPesanan;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(Request $request): View
    {
        $query = Produk::query()
            ->with('gambarUtama')
            ->withSum('itemPesanan as total_terjual', 'jumlah')
            ->withCount('ulasan')
            ->withAvg('ulasan', 'rating');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%")
                    ->orWhere('satuan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('aktif', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('aktif', false);
            }
        }

        match ($request->sort) {
            'nama' => $query->orderBy('nama'),
            'harga_terendah' => $query->orderBy('harga'),
            'harga_tertinggi' => $query->orderByDesc('harga'),
            'terlaris' => $query->orderByDesc('total_terjual'),
            'rating' => $query->orderByDesc('ulasan_avg_rating'),
            default => $query->latest(),
        };

        $produk = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Produk::count(),
            'aktif' => Produk::where('aktif', true)->count(),
            'nonaktif' => Produk::where('aktif', false)->count(),
            'terjual' => (int) ItemPesanan::sum('jumlah'),
        ];

        return view('admin.produk.index', compact('produk', 'stats'));
    }

    public function create(): View
    {
        return view('admin.produk.create', ['produk' => new Produk()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $produk = Produk::create($this->validated($request, true));
        $this->simpanGambar($request, $produk);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan. Stok dapat diatur dari menu Stok.');
    }

    public function edit(Produk $produk): View
    {
        $produk->load('gambarUtama');
        return view('admin.produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $produk->update($this->validated($request, false));
        $this->simpanGambar($request, $produk, true);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function toggle(Produk $produk): RedirectResponse
    {
        $produk->update(['aktif' => ! $produk->aktif]);

        return back()->with('success', $produk->aktif ? 'Produk ditampilkan di katalog pembeli.' : 'Produk disembunyikan dari katalog pembeli.');
    }

    private function validated(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'isi_per_satuan' => ['nullable', 'integer', 'min:0'],
            'berat' => ['nullable', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'masa_simpan' => ['nullable', 'integer', 'min:0'],
            'saran_penyimpanan' => ['nullable', 'string', 'max:255'],
            'saran_penyajian' => ['nullable', 'string', 'max:255'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => false];

        if ($creating) {
            $data['stok'] = 0;
            $data['min_stok'] = 20;
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
