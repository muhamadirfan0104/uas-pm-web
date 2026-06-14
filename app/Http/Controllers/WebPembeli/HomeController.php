<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\PengaturanToko;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function home(): View
    {
        $pengaturan = PengaturanToko::utama();

        $banner = collect();

        $statBeranda = [
            'produk_aktif' => Produk::query()->where('aktif', true)->count(),
            'produk_tersedia' => Produk::query()->where('aktif', true)->where('stok', '>', 0)->count(),
            'total_ulasan' => Ulasan::query()->where('ditampilkan', true)->count(),
            'rata_rating' => round((float) Ulasan::query()->where('ditampilkan', true)->avg('rating'), 1),
        ];

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
            ->get()
            ->filter(fn ($produk) => (int) ($produk->total_terjual ?? 0) > 0)
            ->values();

        $ulasanBeranda = Ulasan::query()
            ->with(['user', 'produk'])
            ->where('ditampilkan', true)
            ->latest()
            ->take(3)
            ->get();

        return view('pembeli.home', compact(
            'pengaturan',
            'banner',
            'produkTerbaru',
            'produkTerlaris',
            'ulasanBeranda',
            'statBeranda'
        ));
    }

    public function produk(Request $request): View
    {
        $pengaturan = PengaturanToko::utama();

        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'terbaru');
        $stok = $request->query('stok', 'semua');

        if (! in_array($sort, ['terbaru', 'termurah', 'termahal', 'rating', 'stok_banyak', 'nama'], true)) {
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
            ->when($sort === 'stok_banyak', function ($query) {
                $query->orderByDesc('stok');
            })
            ->when($sort === 'nama', function ($query) {
                $query->orderBy('nama');
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

    public function detailProduk(Request $request, Produk $produk): View
    {
        abort_unless($produk->aktif, 404);

        $pengaturan = PengaturanToko::utama();

        $produk->load([
            'gambar',
            'gambarUtama',
        ]);

        $filterUlasan = $request->query('filter_ulasan', 'semua');
        if (! in_array($filterUlasan, ['semua', 'foto', 'video', 'bintang5'], true)) {
            $filterUlasan = 'semua';
        }

        $ulasanBase = $produk->ulasan()
            ->where('ditampilkan', true);

        $jumlahUlasan = (clone $ulasanBase)->count();
        $rataRating = round((float) (clone $ulasanBase)->avg('rating'), 1);
        $jumlahUlasanFoto = (clone $ulasanBase)
            ->where(function ($query) {
                $query->whereNotNull('foto_ulasan')
                    ->orWhereHas('media', fn ($media) => $media->where('jenis', 'foto'));
            })
            ->count();
        $jumlahUlasanVideo = (clone $ulasanBase)
            ->where(function ($query) {
                $query->whereNotNull('video_ulasan')
                    ->orWhereHas('media', fn ($media) => $media->where('jenis', 'video'));
            })
            ->count();
        $jumlahUlasanBintang5 = (clone $ulasanBase)->where('rating', 5)->count();
        $ratingDistribusi = [];
        for ($bintang = 5; $bintang >= 1; $bintang--) {
            $ratingDistribusi[$bintang] = (clone $ulasanBase)->where('rating', $bintang)->count();
        }

        $totalTerjual = (int) $produk->itemPesanan()
            ->whereHas('pesanan', function ($query) {
                $query->whereIn('status', ['dibayar', 'diproses', 'siap_diambil', 'dalam_pengantaran', 'selesai']);
            })
            ->sum('jumlah');

        $ulasan = (clone $ulasanBase)
            ->with(['user', 'media'])
            ->when($filterUlasan === 'foto', function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('foto_ulasan')
                        ->orWhereHas('media', fn ($media) => $media->where('jenis', 'foto'));
                });
            })
            ->when($filterUlasan === 'video', function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('video_ulasan')
                        ->orWhereHas('media', fn ($media) => $media->where('jenis', 'video'));
                });
            })
            ->when($filterUlasan === 'bintang5', function ($query) {
                $query->where('rating', 5);
            })
            ->latest()
            ->paginate(6, ['*'], 'ulasan_page')
            ->withQueryString()
            ->fragment('ulasan-produk');

        $produkLain = Produk::query()
            ->with('gambarUtama')
            ->withAvg(['ulasan as rata_rating' => function ($query) {
                $query->where('ditampilkan', true);
            }], 'rating')
            ->withCount(['ulasan as jumlah_ulasan' => function ($query) {
                $query->where('ditampilkan', true);
            }])
            ->withSum('itemPesanan as total_terjual', 'jumlah')
            ->where('aktif', true)
            ->whereKeyNot($produk->id)
            ->latest()
            ->take(4)
            ->get();

        return view('pembeli.detail-produk', compact(
            'produk',
            'pengaturan',
            'ulasan',
            'rataRating',
            'jumlahUlasan',
            'jumlahUlasanFoto',
            'jumlahUlasanVideo',
            'jumlahUlasanBintang5',
            'ratingDistribusi',
            'totalTerjual',
            'filterUlasan',
            'produkLain'
        ));
    }

    public function ulasan(Request $request): View
    {
        $pengaturan = PengaturanToko::utama();

        $filterMedia = $request->query('media', 'semua');
        $filterRating = $request->query('rating', 'semua');
        $search = trim((string) $request->query('search', ''));

        if (! in_array($filterMedia, ['semua', 'foto', 'video'], true)) {
            $filterMedia = 'semua';
        }

        if (! in_array($filterRating, ['semua', '5', '4', '3', '2', '1'], true)) {
            $filterRating = 'semua';
        }

        $baseQuery = Ulasan::query()
            ->where('ditampilkan', true);

        $statUlasan = [
            'total' => (clone $baseQuery)->count(),
            'rata_rating' => round((float) (clone $baseQuery)->avg('rating'), 1),
            'foto' => (clone $baseQuery)
                ->where(function ($query) {
                    $query->whereNotNull('foto_ulasan')
                        ->orWhereHas('media', fn ($media) => $media->where('jenis', 'foto'));
                })
                ->count(),
            'video' => (clone $baseQuery)
                ->where(function ($query) {
                    $query->whereNotNull('video_ulasan')
                        ->orWhereHas('media', fn ($media) => $media->where('jenis', 'video'));
                })
                ->count(),
        ];

        $ulasanList = (clone $baseQuery)
            ->with(['user', 'produk.gambarUtama', 'media'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('komentar', 'like', '%' . $search . '%')
                        ->orWhereHas('produk', fn ($produk) => $produk->where('nama', 'like', '%' . $search . '%'))
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->when($filterMedia === 'foto', function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('foto_ulasan')
                        ->orWhereHas('media', fn ($media) => $media->where('jenis', 'foto'));
                });
            })
            ->when($filterMedia === 'video', function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('video_ulasan')
                        ->orWhereHas('media', fn ($media) => $media->where('jenis', 'video'));
                });
            })
            ->when($filterRating !== 'semua', function ($query) use ($filterRating) {
                $query->where('rating', (int) $filterRating);
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('pembeli.ulasan', compact(
            'pengaturan',
            'ulasanList',
            'statUlasan',
            'filterMedia',
            'filterRating',
            'search'
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