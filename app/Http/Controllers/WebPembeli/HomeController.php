<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\PengaturanToko;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function home(): View
    {
        $pengaturan = PengaturanToko::query()->first();

        if (! $pengaturan) {
            $pengaturan = new PengaturanToko([
                'nama' => 'SiTahu',
                'telepon' => '',
                'alamat' => 'Alamat toko belum diatur.',
                'jam_buka' => null,
                'jam_tutup' => null,
                'tentang' => 'Toko tahu rumahan yang menyediakan berbagai pilihan tahu segar untuk kebutuhan harian.',
                'area_pengiriman' => 'Area pengiriman akan diinformasikan saat pemesanan.',
            ]);
        }

        $produkTerbaru = Produk::query()
            ->with('gambarUtama')
            ->where('aktif', true)
            ->latest()
            ->take(4)
            ->get();

        $produkTerlaris = Produk::query()
            ->with('gambarUtama')
            ->withSum('itemPesanan as total_terjual', 'jumlah')
            ->where('aktif', true)
            ->orderByDesc('total_terjual')
            ->take(3)
            ->get()
            ->filter(fn ($produk) => (int) ($produk->total_terjual ?? 0) > 0);

        return view('pembeli.home', compact(
            'pengaturan',
            'produkTerbaru',
            'produkTerlaris'
        ));
    }

    public function produk(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $sort = $request->query('sort', 'terbaru');
        $stok = $request->query('stok', 'semua');

        $produkQuery = Produk::query()
            ->with('gambarUtama')
            ->where('aktif', true);

        if ($search !== '') {
            $produkQuery->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%')
                    ->orWhere('satuan', 'like', '%' . $search . '%');
            });
        }

        if ($stok === 'tersedia') {
            $produkQuery->where('stok', '>', 0);
        }

        if ($stok === 'habis') {
            $produkQuery->where('stok', '<=', 0);
        }

        if ($sort === 'termurah') {
            $produkQuery->orderBy('harga', 'asc');
        } elseif ($sort === 'termahal') {
            $produkQuery->orderBy('harga', 'desc');
        } elseif ($sort === 'stok_banyak') {
            $produkQuery->orderBy('stok', 'desc');
        } elseif ($sort === 'nama') {
            $produkQuery->orderBy('nama', 'asc');
        } else {
            $produkQuery->latest();
        }

        $produkList = $produkQuery
            ->paginate(12)
            ->withQueryString();

        return view('pembeli.produk', compact(
            'produkList',
            'search',
            'sort',
            'stok'
        ));
    }

    public function detailProduk(Produk $produk): View
    {
        if (! $produk->aktif) {
            abort(404);
        }

        $produk->load(['gambar', 'gambarUtama']);

        $pengaturan = PengaturanToko::query()->first();

        if (! $pengaturan) {
            $pengaturan = new PengaturanToko([
                'nama' => 'SiTahu',
                'telepon' => '',
                'alamat' => 'Alamat toko belum diatur.',
                'tentang' => 'Toko tahu rumahan yang menyediakan berbagai pilihan tahu segar untuk kebutuhan harian.',
            ]);
        }

        $ulasan = Ulasan::query()
            ->with('user')
            ->where('produk_id', $produk->id)
            ->where('ditampilkan', true)
            ->latest()
            ->take(6)
            ->get();

        $rataRating = $ulasan->count()
            ? round($ulasan->avg('rating'), 1)
            : null;

        $produkLain = Produk::query()
            ->with('gambarUtama')
            ->where('aktif', true)
            ->where('id', '!=', $produk->id)
            ->latest()
            ->take(4)
            ->get();

        return view('pembeli.detail-produk', compact(
            'produk',
            'pengaturan',
            'ulasan',
            'rataRating',
            'produkLain'
        ));
    }

    public function profil(): View
    {
        $pengaturan = PengaturanToko::query()->first();

        if (! $pengaturan) {
            $pengaturan = new PengaturanToko([
                'nama' => 'SiTahu',
                'telepon' => '',
                'email' => '',
                'alamat' => 'Alamat toko belum diatur.',
                'jam_buka' => null,
                'jam_tutup' => null,
                'tentang' => 'Toko tahu rumahan yang menyediakan berbagai pilihan tahu segar untuk kebutuhan harian.',
                'area_pengiriman' => 'Area pengiriman akan diinformasikan saat pemesanan.',
                'info_pembayaran' => 'Pembayaran dapat dilakukan melalui QRIS atau tunai.',
            ]);
        }

        return view('pembeli.profil', compact('pengaturan'));
    }

    public function comingSoon(): View
    {
        return view('pembeli.coming-soon');
    }
}
