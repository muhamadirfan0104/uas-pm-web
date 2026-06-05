<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        $filterBulan = (int) $request->input('bulan', now()->month);
        $filterTahun = (int) $request->input('tahun', now()->year);

        if ($filterBulan < 1 || $filterBulan > 12) {
            $filterBulan = now()->month;
        }

        if ($filterTahun < 2020 || $filterTahun > now()->year + 1) {
            $filterTahun = now()->year;
        }

        $startOfPeriod = Carbon::create($filterTahun, $filterBulan, 1)->startOfDay();
        $endOfPeriod = $startOfPeriod->copy()->endOfMonth()->endOfDay();
        $periodeLabel = $startOfPeriod->translatedFormat('F Y');

        $paidInPeriod = Pembayaran::query()
            ->where('status', 'dibayar')
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod]);

        $pesananInPeriod = Pesanan::query()
            ->whereBetween('tanggal_pesanan', [$startOfPeriod, $endOfPeriod]);

        $statusPesanan = Pesanan::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'penjualan_hari_ini' => Pembayaran::query()
                ->where('status', 'dibayar')
                ->whereDate('created_at', $today)
                ->sum('jumlah'),

            'penjualan_periode' => (clone $paidInPeriod)->sum('jumlah'),

            'penjualan_semua' => Pembayaran::query()
                ->where('status', 'dibayar')
                ->sum('jumlah'),

            'pesanan_hari_ini' => Pesanan::query()
                ->whereDate('tanggal_pesanan', $today)
                ->count(),

            'pesanan_periode' => (clone $pesananInPeriod)->count(),

            'pesanan_semua' => Pesanan::query()->count(),

            'menunggu_pembayaran' => Pesanan::query()
                ->where('status_pembayaran', 'menunggu_pembayaran')
                ->count(),

            'dibayar' => (int) ($statusPesanan['dibayar'] ?? 0),
            'diproses' => (int) ($statusPesanan['diproses'] ?? 0),
            'siap_diambil' => (int) ($statusPesanan['siap_diambil'] ?? 0),
            'dalam_pengantaran' => (int) ($statusPesanan['dalam_pengantaran'] ?? 0),
            'selesai' => (int) ($statusPesanan['selesai'] ?? 0),
            'dibatalkan' => (int) ($statusPesanan['dibatalkan'] ?? 0),

            'produk_aktif' => Produk::query()
                ->where('aktif', true)
                ->count(),

            'stok_menipis' => Produk::query()
                ->where('stok', '>', 0)
                ->whereColumn('stok', '<=', 'min_stok')
                ->count(),

            'stok_habis' => Produk::query()
                ->where('stok', '<=', 0)
                ->count(),

            'total_pembeli' => User::query()
                ->where('role', 'pembeli')
                ->count(),

            'total_ulasan' => Ulasan::query()->count(),

            'ulasan_video' => Ulasan::query()
                ->whereNotNull('video_ulasan')
                ->count(),
        ];

        $produkTerlaris = Produk::query()
            ->with(['gambarUtama'])
            ->withSum([
                'itemPesanan as total_terjual' => function ($query) use ($startOfPeriod, $endOfPeriod) {
                    $query->whereHas('pesanan', function ($pesananQuery) use ($startOfPeriod, $endOfPeriod) {
                        $pesananQuery->whereBetween('tanggal_pesanan', [$startOfPeriod, $endOfPeriod])
                            ->whereNotIn('status', ['dibatalkan']);
                    });
                },
            ], 'jumlah')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $stokPerhatian = Produk::query()
            ->with(['gambarUtama'])
            ->where(function ($query) {
                $query->where('stok', '<=', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('stok', '>', 0)
                            ->whereColumn('stok', '<=', 'min_stok');
                    });
            })
            ->orderBy('stok')
            ->limit(6)
            ->get();

        $pesananTerbaru = Pesanan::query()
            ->with(['user', 'pembayaran', 'pengiriman'])
            ->latest('tanggal_pesanan')
            ->limit(6)
            ->get();

        $penjualanHarian = collect(range(1, $startOfPeriod->daysInMonth))->map(function ($day) use ($startOfPeriod) {
            $tanggal = $startOfPeriod->copy()->day($day);

            return [
                'label' => $tanggal->format('d'),
                'total' => (float) Pembayaran::query()
                    ->where('status', 'dibayar')
                    ->whereDate('created_at', $tanggal)
                    ->sum('jumlah'),
            ];
        });

        $maxPenjualan = max((float) $penjualanHarian->max('total'), 1);

        $daftarBulan = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create(null, $bulan, 1)->translatedFormat('F')];
        });

        $daftarTahun = range(now()->year + 1, max(2020, now()->year - 5));

        return view('admin.dashboard', compact(
            'stats',
            'produkTerlaris',
            'stokPerhatian',
            'pesananTerbaru',
            'penjualanHarian',
            'maxPenjualan',
            'filterBulan',
            'filterTahun',
            'daftarBulan',
            'daftarTahun',
            'periodeLabel'
        ));
    }
}