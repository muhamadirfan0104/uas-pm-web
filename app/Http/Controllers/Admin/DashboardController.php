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

        $paidInPeriod = Pembayaran::where('status', 'dibayar')
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod]);

        $pesananInPeriod = Pesanan::whereBetween('tanggal_pesanan', [$startOfPeriod, $endOfPeriod]);

        $stats = [
            'total_penjualan' => (clone $paidInPeriod)->sum('jumlah'),
            'total_penjualan_all' => Pembayaran::where('status', 'dibayar')->sum('jumlah'),
            'penjualan_hari_ini' => Pembayaran::where('status', 'dibayar')->whereDate('created_at', $today)->sum('jumlah'),
            'penjualan_bulan_ini' => (clone $paidInPeriod)->sum('jumlah'),
            'total_pesanan' => (clone $pesananInPeriod)->count(),
            'total_pesanan_all' => Pesanan::count(),
            'pesanan_hari_ini' => Pesanan::whereDate('tanggal_pesanan', $today)->count(),
            'pesanan_diproses' => Pesanan::where('status', 'diproses')->count(),
            'menunggu_pembayaran' => Pesanan::where('status_pembayaran', 'menunggu_pembayaran')->count(),
            'produk_aktif' => Produk::where('aktif', true)->count(),
            'stok_menipis' => Produk::where('stok', '>', 0)->whereColumn('stok', '<=', 'min_stok')->count(),
            'stok_habis' => Produk::where('stok', '<=', 0)->count(),
            'total_pembeli' => User::where('role', 'pembeli')->count(),
            'total_ulasan' => Ulasan::count(),
        ];

        $produkTerlaris = Produk::withSum([
                'itemPesanan as total_terjual' => function ($query) use ($startOfPeriod, $endOfPeriod) {
                    $query->whereHas('pesanan', function ($pesananQuery) use ($startOfPeriod, $endOfPeriod) {
                        $pesananQuery->whereBetween('tanggal_pesanan', [$startOfPeriod, $endOfPeriod]);
                    });
                },
            ], 'jumlah')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $pesananTerbaru = Pesanan::with(['user', 'pembayaran'])
            ->whereBetween('tanggal_pesanan', [$startOfPeriod, $endOfPeriod])
            ->latest('tanggal_pesanan')
            ->limit(6)
            ->get();

        $penjualanMingguan = collect(range(1, $startOfPeriod->daysInMonth))->map(function ($day) use ($startOfPeriod) {
            $tanggal = $startOfPeriod->copy()->day($day);

            return [
                'label' => $tanggal->format('d'),
                'total' => (float) Pembayaran::where('status', 'dibayar')
                    ->whereDate('created_at', $tanggal)
                    ->sum('jumlah'),
            ];
        });

        $maxPenjualan = max($penjualanMingguan->max('total'), 1);

        $daftarBulan = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create(null, $bulan, 1)->translatedFormat('F')];
        });

        $daftarTahun = range(now()->year + 1, max(2020, now()->year - 5));

        return view('admin.dashboard', compact(
            'stats',
            'produkTerlaris',
            'pesananTerbaru',
            'penjualanMingguan',
            'maxPenjualan',
            'filterBulan',
            'filterTahun',
            'daftarBulan',
            'daftarTahun',
            'periodeLabel'
        ));
    }
}
