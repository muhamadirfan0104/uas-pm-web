<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemPesanan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

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

        $startOfPrevPeriod = $startOfPeriod->copy()->subMonthNoOverflow()->startOfMonth();
        $endOfPrevPeriod = $startOfPrevPeriod->copy()->endOfMonth()->endOfDay();

        $paidInPeriod = Pembayaran::query()
            ->where('status', 'dibayar')
            ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod]);

        $paidPrevPeriod = Pembayaran::query()
            ->where('status', 'dibayar')
            ->whereBetween('created_at', [$startOfPrevPeriod, $endOfPrevPeriod]);

        $pesananInPeriod = Pesanan::query()
            ->whereBetween('tanggal_pesanan', [$startOfPeriod, $endOfPeriod]);

        $statusPesanan = Pesanan::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $penjualanHariIni = Pembayaran::query()
            ->where('status', 'dibayar')
            ->whereDate('created_at', $today)
            ->sum('jumlah');

        $penjualanKemarin = Pembayaran::query()
            ->where('status', 'dibayar')
            ->whereDate('created_at', $yesterday)
            ->sum('jumlah');

        $penjualanPeriode = (clone $paidInPeriod)->sum('jumlah');
        $penjualanPeriodeSebelumnya = (clone $paidPrevPeriod)->sum('jumlah');
        $pertumbuhanPeriode = $penjualanPeriodeSebelumnya > 0
            ? (($penjualanPeriode - $penjualanPeriodeSebelumnya) / $penjualanPeriodeSebelumnya) * 100
            : null;

        $pesananHariIni = Pesanan::query()
            ->whereDate('tanggal_pesanan', $today)
            ->count();

        $pesananKemarin = Pesanan::query()
            ->whereDate('tanggal_pesanan', $yesterday)
            ->count();

        $pesananPeriode = (clone $pesananInPeriod)->count();
        $nilaiRataRataPesanan = $pesananPeriode > 0 ? $penjualanPeriode / $pesananPeriode : 0;

        $stats = [
            'penjualan_hari_ini' => $penjualanHariIni,
            'penjualan_kemarin' => $penjualanKemarin,
            'selisih_penjualan_hari_ini' => $penjualanHariIni - $penjualanKemarin,

            'penjualan_periode' => $penjualanPeriode,
            'penjualan_periode_sebelumnya' => $penjualanPeriodeSebelumnya,
            'pertumbuhan_periode' => $pertumbuhanPeriode,
            'nilai_rata_rata_pesanan' => $nilaiRataRataPesanan,

            'penjualan_semua' => Pembayaran::query()
                ->where('status', 'dibayar')
                ->sum('jumlah'),

            'pesanan_hari_ini' => $pesananHariIni,
            'pesanan_kemarin' => $pesananKemarin,
            'selisih_pesanan_hari_ini' => $pesananHariIni - $pesananKemarin,
            'pesanan_periode' => $pesananPeriode,
            'pesanan_semua' => Pesanan::query()->count(),

            'menunggu_pembayaran' => Pesanan::query()
                ->where('status_pembayaran', 'menunggu_pembayaran')
                ->count(),

            'transfer_perlu_verifikasi' => Pembayaran::query()
                ->where('status', 'menunggu_pembayaran')
                ->whereNotNull('bukti_transfer')
                ->count(),

            'transfer_belum_bukti' => Pembayaran::query()
                ->where('status', 'menunggu_pembayaran')
                ->where('metode_pembayaran', 'transfer_bank')
                ->whereNull('bukti_transfer')
                ->count(),

            'pembayaran_ditolak' => Pembayaran::query()
                ->where('status', 'ditolak')
                ->count(),

            'diproses' => (int) ($statusPesanan['diproses'] ?? 0),
            'siap_diambil' => (int) ($statusPesanan['siap_diambil'] ?? 0),
            'dalam_pengantaran' => (int) ($statusPesanan['dalam_pengantaran'] ?? 0),
            'selesai' => (int) ($statusPesanan['selesai'] ?? 0),
            'dibatalkan' => (int) ($statusPesanan['dibatalkan'] ?? 0),

            'produk_aktif' => Produk::query()
                ->where('aktif', true)
                ->count(),

            'produk_nonaktif' => Produk::query()
                ->where('aktif', false)
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

            'pembeli_baru_periode' => User::query()
                ->where('role', 'pembeli')
                ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
                ->count(),

            'total_ulasan' => Ulasan::query()->count(),

            'rating_rata_rata' => round((float) Ulasan::query()->avg('rating'), 1),

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
            ->limit(6)
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
            ->with(['user', 'pembayaran', 'pengiriman', 'item.produk.gambarUtama'])
            ->latest('tanggal_pesanan')
            ->limit(6)
            ->get();

        $pembayaranPerluDicek = Pembayaran::query()
            ->with(['pesanan.user'])
            ->where('status', 'menunggu_pembayaran')
            ->whereNotNull('bukti_transfer')
            ->latest()
            ->limit(5)
            ->get();

        $ulasanTerbaru = Ulasan::query()
            ->with(['user', 'produk.gambarUtama'])
            ->latest()
            ->limit(5)
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

        $statusOperasional = collect([
            ['key' => 'menunggu_pembayaran', 'label' => 'Belum Bayar', 'value' => (int) ($stats['menunggu_pembayaran'] ?? 0), 'icon' => 'bi-hourglass-split'],
            ['key' => 'diproses', 'label' => 'Diproses', 'value' => (int) ($stats['diproses'] ?? 0), 'icon' => 'bi-gear'],
            ['key' => 'siap_diambil', 'label' => 'Siap Diambil', 'value' => (int) ($stats['siap_diambil'] ?? 0), 'icon' => 'bi-bag-check'],
            ['key' => 'dalam_pengantaran', 'label' => 'Dikirim', 'value' => (int) ($stats['dalam_pengantaran'] ?? 0), 'icon' => 'bi-truck'],
            ['key' => 'selesai', 'label' => 'Selesai', 'value' => (int) ($stats['selesai'] ?? 0), 'icon' => 'bi-check2-circle'],
        ]);

        $maxStatus = max((int) $statusOperasional->max('value'), 1);

        $metodePembayaran = Pembayaran::query()
            ->selectRaw('metode_pembayaran, COUNT(*) as total')
            ->groupBy('metode_pembayaran')
            ->pluck('total', 'metode_pembayaran');

        $metodePembayaran = collect([
            ['label' => 'Transfer Bank', 'value' => (int) ($metodePembayaran['transfer_bank'] ?? 0), 'icon' => 'bi-bank'],
            ['label' => 'COD', 'value' => (int) ($metodePembayaran['cod'] ?? 0), 'icon' => 'bi-cash-coin'],
        ]);

        $totalMetodePembayaran = max((int) $metodePembayaran->sum('value'), 1);

        $prioritas = collect([
            [
                'label' => 'Verifikasi bukti transfer',
                'desc' => 'Cek bukti pembayaran yang sudah diunggah pembeli.',
                'value' => (int) ($stats['transfer_perlu_verifikasi'] ?? 0),
                'route' => route('admin.pembayaran.index'),
                'icon' => 'bi-shield-check',
                'tone' => 'warning',
            ],
            [
                'label' => 'Pesanan siap diproses',
                'desc' => 'Pesanan transfer sudah diterima atau COD yang otomatis masuk proses.',
                'value' => (int) ($stats['diproses'] ?? 0),
                'route' => route('admin.pesanan.index'),
                'icon' => 'bi-box-seam',
                'tone' => 'primary',
            ],
            [
                'label' => 'Stok perlu tindakan',
                'desc' => 'Produk habis atau sudah menyentuh batas minimum.',
                'value' => (int) (($stats['stok_menipis'] ?? 0) + ($stats['stok_habis'] ?? 0)),
                'route' => route('admin.stok.index'),
                'icon' => 'bi-exclamation-triangle',
                'tone' => 'danger',
            ],
        ])->sortByDesc('value')->values();

        $daftarBulan = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create(null, $bulan, 1)->translatedFormat('F')];
        });

        $daftarTahun = range(now()->year + 1, max(2020, now()->year - 5));

        return view('admin.dashboard', compact(
            'stats',
            'produkTerlaris',
            'stokPerhatian',
            'pesananTerbaru',
            'pembayaranPerluDicek',
            'ulasanTerbaru',
            'penjualanHarian',
            'maxPenjualan',
            'statusOperasional',
            'maxStatus',
            'metodePembayaran',
            'totalMetodePembayaran',
            'prioritas',
            'filterBulan',
            'filterTahun',
            'daftarBulan',
            'daftarTahun',
            'periodeLabel'
        ));
    }
}
