<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\PengaturanToko;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function home(): View
    {
        $pengaturan = PengaturanToko::utama();

        $banner = Banner::query()
            ->where('aktif', true)
            ->latest()
            ->take(5)
            ->get();

        $produkTerbaru = Produk::query()
            ->with(['gambarUtama'])
            ->withAvg(['ulasan as rata_rating' => function ($query) {
                $query->where('ditampilkan', true);
            }], 'rating')
            ->withCount(['ulasan as jumlah_ulasan' => function ($query) {
                $query->where('ditampilkan', true);
            }])
            ->where('aktif', true)
            ->latest()
            ->take(8)
            ->get();

        $produkTerlaris = Produk::query()
            ->with(['gambarUtama'])
            ->withAvg(['ulasan as rata_rating' => function ($query) {
                $query->where('ditampilkan', true);
            }], 'rating')
            ->withCount(['ulasan as jumlah_ulasan' => function ($query) {
                $query->where('ditampilkan', true);
            }])
            ->where('aktif', true)
            ->withSum('itemPesanan as total_terjual', 'jumlah')
            ->orderByDesc('total_terjual')
            ->take(8)
            ->get();

        return view('pembeli.home', compact(
            'pengaturan',
            'banner',
            'produkTerbaru',
            'produkTerlaris'
        ));
    }

    public function produk(Request $request): View
    {
        $pengaturan = PengaturanToko::utama();

        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'terbaru');
        $stok = $request->query('stok', 'semua');

        if (! in_array($sort, ['terbaru', 'termurah', 'termahal', 'rating'], true)) {
            $sort = 'terbaru';
        }

        if (! in_array($stok, ['semua', 'tersedia', 'habis'], true)) {
            $stok = 'semua';
        }

        $produkList = Produk::query()
            ->with(['gambarUtama'])
            ->withAvg(['ulasan as rata_rating' => function ($query) {
                $query->where('ditampilkan', true);
            }], 'rating')
            ->withCount(['ulasan as jumlah_ulasan' => function ($query) {
                $query->where('ditampilkan', true);
            }])
            ->where('aktif', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%')
                        ->orWhere('satuan', 'like', '%' . $search . '%');
                });
            })
            ->when($stok === 'tersedia', function ($query) {
                $query->where('stok', '>', 0);
            })
            ->when($stok === 'habis', function ($query) {
                $query->where('stok', '<=', 0);
            })
            ->when($sort === 'termurah', function ($query) {
                $query->orderBy('harga');
            })
            ->when($sort === 'termahal', function ($query) {
                $query->orderByDesc('harga');
            })
            ->when($sort === 'rating', function ($query) {
                $query->orderByDesc('rata_rating');
            })
            ->when($sort === 'terbaru', function ($query) {
                $query->latest();
            })
            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Catatan:
        |--------------------------------------------------------------------------
        | View produk.blade.php kamu memakai $produkList.
        | Tapi beberapa kode lama mungkin masih memakai $produk.
        | Jadi dua-duanya dikirim biar aman.
        */
        $produk = $produkList;

        return view('pembeli.produk', compact(
            'pengaturan',
            'produkList',
            'produk',
            'search',
            'sort',
            'stok'
        ));
    }

    public function detailProduk(Produk $produk): View
    {
        abort_unless($produk->aktif, 404);

        $produk->load([
            'gambar',
            'gambarUtama',
            'ulasan.user',
        ]);

        $ulasan = $produk->ulasan()
            ->with('user')
            ->where('ditampilkan', true)
            ->latest()
            ->get();

        $rataRating = round((float) $ulasan->avg('rating'), 1);
        $jumlahUlasan = $ulasan->count();

        return view('pembeli.detail-produk', compact(
            'produk',
            'ulasan',
            'rataRating',
            'jumlahUlasan'
        ));
    }

    public function profil(): View
    {
        $pengaturan = PengaturanToko::utama();
        $user = Auth::user();

        $pesananQuery = Pesanan::query()
            ->where('user_id', $user->id);

        $statProfil = [
            'total_pesanan' => (clone $pesananQuery)->count(),

            'pesanan_aktif' => (clone $pesananQuery)
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count(),

            'pesanan_selesai' => (clone $pesananQuery)
                ->where('status', 'selesai')
                ->count(),

            'total_belanja' => (clone $pesananQuery)
                ->where('status_pembayaran', 'dibayar')
                ->sum('total_bayar'),
        ];

        $pesananTerbaru = Pesanan::query()
            ->with(['item.produk', 'pembayaran', 'pengiriman'])
            ->where('user_id', $user->id)
            ->latest('tanggal_pesanan')
            ->take(4)
            ->get();

        return view('pembeli.profil', compact(
            'pengaturan',
            'user',
            'statProfil',
            'pesananTerbaru'
        ));
    }

    public function comingSoon(): View
    {
        return view('pembeli.coming-soon');
    }
}