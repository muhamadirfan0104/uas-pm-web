<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemPesanan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class LaporanController extends Controller
{
    private array $jenisLaporan = ['penjualan', 'pembeli', 'produk', 'pembayaran', 'stok'];

    public function index(Request $request): View
    {
        [$tanggalMulai, $tanggalSelesai] = $this->periode($request);
        $jenis = in_array($request->input('jenis'), $this->jenisLaporan, true) ? $request->input('jenis') : 'penjualan';

        $statusFilter = $request->input('status');
        $metodeBayarFilter = $request->input('metode_pembayaran');
        $metodeAmbilFilter = $request->input('metode_pengambilan');
        $q = trim((string) $request->input('q', ''));

        $pesananBase = Pesanan::query()
            ->with(['user', 'pembayaran', 'pengiriman', 'item.produk.gambarUtama'])
            ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai]);

        $pesananFilter = (clone $pesananBase)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_invoice', 'like', '%' . $q . '%')
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', '%' . $q . '%')
                                ->orWhere('email', 'like', '%' . $q . '%')
                                ->orWhere('telepon', 'like', '%' . $q . '%');
                        })
                        ->orWhereHas('item.produk', fn ($produkQuery) => $produkQuery->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
            ->when($metodeAmbilFilter, fn ($query) => $query->where('metode_pengambilan', $metodeAmbilFilter))
            ->when($metodeBayarFilter, function ($query) use ($metodeBayarFilter) {
                $query->whereHas('pembayaran', fn ($paymentQuery) => $paymentQuery->where('metode_pembayaran', $metodeBayarFilter));
            });

        $pembayaranBase = Pembayaran::query()
            ->with('pesanan.user')
            ->whereHas('pesanan', fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai]));

        $pembayaranDibayar = (clone $pembayaranBase)->where('status', 'dibayar');

        $stats = [
            'pendapatan' => (clone $pembayaranDibayar)->sum('jumlah'),
            'transaksi_dibayar' => (clone $pembayaranDibayar)->count(),
            'pesanan_total' => (clone $pesananBase)->count(),
            'pesanan_selesai' => (clone $pesananBase)->where('status', 'selesai')->count(),
            'pesanan_batal' => (clone $pesananBase)->where('status', 'dibatalkan')->count(),
            'pesanan_aktif' => (clone $pesananBase)->whereNotIn('status', ['selesai', 'dibatalkan'])->count(),
            'produk_terjual' => ItemPesanan::query()
                ->whereHas('pesanan', fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereNotIn('status', ['dibatalkan']))
                ->sum('jumlah'),
        ];
        $stats['rata_transaksi'] = $stats['transaksi_dibayar'] > 0 ? $stats['pendapatan'] / $stats['transaksi_dibayar'] : 0;

        $pesananHarianMap = Pesanan::query()
            ->selectRaw('DATE(tanggal_pesanan) as tanggal, COUNT(*) as total')
            ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
            ->groupByRaw('DATE(tanggal_pesanan)')
            ->pluck('total', 'tanggal');

        $pembayaranHarianMap = Pembayaran::query()
            ->join('pesanan', 'pesanan.id', '=', 'pembayaran.pesanan_id')
            ->selectRaw('DATE(pesanan.tanggal_pesanan) as tanggal, SUM(pembayaran.jumlah) as total')
            ->where('pembayaran.status', 'dibayar')
            ->whereBetween('pesanan.tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
            ->groupByRaw('DATE(pesanan.tanggal_pesanan)')
            ->pluck('total', 'tanggal');

        $laporanHarian = collect();
        $cursor = $tanggalMulai->copy()->startOfDay();

        while ($cursor->lessThanOrEqualTo($tanggalSelesai)) {
            $keyTanggal = $cursor->format('Y-m-d');

            $laporanHarian->push([
                'tanggal' => $cursor->format('d M'),
                'tanggal_lengkap' => $cursor->format('d/m/Y'),
                'pesanan' => (int) ($pesananHarianMap[$keyTanggal] ?? 0),
                'pendapatan' => (float) ($pembayaranHarianMap[$keyTanggal] ?? 0),
            ]);

            $cursor->addDay();
        }

        $statusPesanan = (clone $pesananBase)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $statusPembayaran = (clone $pembayaranBase)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $metodePembayaran = (clone $pembayaranBase)->selectRaw('metode_pembayaran, COUNT(*) as total')->groupBy('metode_pembayaran')->pluck('total', 'metode_pembayaran');
        $metodePengambilan = (clone $pesananBase)->selectRaw('metode_pengambilan, COUNT(*) as total')->groupBy('metode_pengambilan')->pluck('total', 'metode_pengambilan');

        $produkTerlaris = Produk::query()
            ->with('gambarUtama')
            ->withSum(['itemPesanan as total_terjual' => function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereHas('pesanan', fn ($pesanan) => $pesanan->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereNotIn('status', ['dibatalkan']));
            }], 'jumlah')
            ->withSum(['itemPesanan as total_pendapatan_produk' => function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereHas('pesanan', fn ($pesanan) => $pesanan->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar')));
            }], 'subtotal')
            ->having('total_terjual', '>', 0)
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $produkStokPerhatian = Produk::query()
            ->with('gambarUtama')
            ->where(fn ($query) => $query->where('stok', '<=', 0)->orWhere(fn ($sub) => $sub->where('stok', '>', 0)->whereColumn('stok', '<=', 'min_stok')))
            ->orderBy('stok')
            ->limit(8)
            ->get();

        $pembeliStats = [
            'total' => User::where('role', 'pembeli')->count(),
            'baru' => User::where('role', 'pembeli')->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->count(),
            'aktif' => User::where('role', 'pembeli')->whereHas('pesanan', fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai]))->count(),
            'memberi_ulasan' => User::where('role', 'pembeli')->whereHas('ulasan', fn ($query) => $query->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]))->count(),
        ];

        $topPembeli = User::query()
            ->where('role', 'pembeli')
            ->withCount(['pesanan as total_pesanan_periode' => fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])])
            ->withSum(['pesanan as total_belanja_periode' => fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar'))], 'total_bayar')
            ->having('total_pesanan_periode', '>', 0)
            ->orderByDesc('total_belanja_periode')
            ->limit(10)
            ->get();

        $ulasanStats = [
            'total' => Ulasan::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->count(),
            'rata' => (float) (Ulasan::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->avg('rating') ?: 0),
            'rendah' => Ulasan::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->where('rating', '<=', 3)->count(),
        ];

        $produkStats = [
            'total' => Produk::count(),
            'aktif' => Produk::where('aktif', true)->count(),
            'nonaktif' => Produk::where('aktif', false)->count(),
            'stok_menipis' => Produk::where('stok', '>', 0)->whereColumn('stok', '<=', 'min_stok')->count(),
            'stok_habis' => Produk::where('stok', '<=', 0)->count(),
        ];

        $pembayaranStats = [
            'menunggu_upload' => (clone $pembayaranBase)->where('metode_pembayaran', 'transfer_bank')->where('status', 'menunggu_pembayaran')->count(),
            'menunggu_verifikasi' => (clone $pembayaranBase)->where('status', 'menunggu_verifikasi')->count(),
            'ditolak' => (clone $pembayaranBase)->where('status', 'ditolak')->count(),
            'dibayar' => (clone $pembayaranBase)->where('status', 'dibayar')->count(),
        ];

        $dataTable = match ($jenis) {
            'pembeli' => User::query()
                ->where('role', 'pembeli')
                ->when($q, fn ($query) => $query->where(fn ($sub) => $sub->where('name', 'like', '%' . $q . '%')->orWhere('email', 'like', '%' . $q . '%')->orWhere('telepon', 'like', '%' . $q . '%')))
                ->withCount(['pesanan as total_pesanan_periode' => fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])])
                ->withSum(['pesanan as total_belanja_periode' => fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar'))], 'total_bayar')
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'produk' => Produk::query()
                ->with('gambarUtama')
                ->when($q, fn ($query) => $query->where('nama', 'like', '%' . $q . '%'))
                ->withSum(['itemPesanan as total_terjual' => fn ($query) => $query->whereHas('pesanan', fn ($pesanan) => $pesanan->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereNotIn('status', ['dibatalkan']))], 'jumlah')
                ->withSum(['itemPesanan as total_pendapatan_produk' => fn ($query) => $query->whereHas('pesanan', fn ($pesanan) => $pesanan->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar')))], 'subtotal')
                ->orderByDesc('total_terjual')
                ->paginate(12)
                ->withQueryString(),
            'pembayaran' => (clone $pembayaranBase)
                ->when($q, fn ($query) => $query->where(fn ($sub) => $sub->where('referensi_pembayaran', 'like', '%' . $q . '%')->orWhereHas('pesanan', fn ($pesanan) => $pesanan->where('nomor_invoice', 'like', '%' . $q . '%')->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%' . $q . '%')))))
                ->when($metodeBayarFilter, fn ($query) => $query->where('metode_pembayaran', $metodeBayarFilter))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'stok' => RiwayatStok::query()
                ->with('produk')
                ->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])
                ->when($q, fn ($query) => $query->whereHas('produk', fn ($produk) => $produk->where('nama', 'like', '%' . $q . '%')))
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(12)
                ->withQueryString(),
            default => (clone $pesananFilter)->latest('tanggal_pesanan')->paginate(12)->withQueryString(),
        };

        if ($jenis === 'stok' && method_exists($dataTable, 'getCollection')) {
            $dataTable->setCollection($this->hydrateMutasiStok($dataTable->getCollection()));
        }

        return view('admin.laporan.index', compact(
            'tanggalMulai', 'tanggalSelesai', 'jenis', 'dataTable', 'stats', 'laporanHarian',
            'statusPesanan', 'statusPembayaran', 'metodePembayaran', 'metodePengambilan',
            'produkTerlaris', 'produkStokPerhatian', 'pembeliStats', 'topPembeli', 'ulasanStats',
            'produkStats', 'pembayaranStats'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$tanggalMulai, $tanggalSelesai] = $this->periode($request);
        $jenis = in_array($request->input('jenis'), $this->jenisLaporan, true) ? $request->input('jenis') : 'penjualan';
        $namaFile = 'laporan-' . $jenis . '-sitahu-' . $tanggalMulai->format('Ymd') . '-' . $tanggalSelesai->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($jenis, $tanggalMulai, $tanggalSelesai) {
            $handle = fopen('php://output', 'w');

            if ($jenis === 'pembeli') {
                fputcsv($handle, ['Nama Pembeli', 'Email', 'Telepon', 'Total Pesanan Periode', 'Total Belanja Periode', 'Tanggal Daftar']);
                User::query()->where('role', 'pembeli')
                    ->withCount(['pesanan as total_pesanan_periode' => fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])])
                    ->withSum(['pesanan as total_belanja_periode' => fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar'))], 'total_bayar')
                    ->orderBy('name')
                    ->chunk(200, function ($users) use ($handle) {
                        foreach ($users as $user) {
                            fputcsv($handle, [$user->name, $user->email, $user->telepon, $user->total_pesanan_periode, $user->total_belanja_periode ?? 0, optional($user->created_at)->format('Y-m-d H:i')]);
                        }
                    });
            } elseif ($jenis === 'produk') {
                fputcsv($handle, ['Produk', 'Harga', 'Stok', 'Minimal Stok', 'Status', 'Terjual Periode', 'Pendapatan Periode']);
                Produk::query()
                    ->withSum(['itemPesanan as total_terjual' => fn ($query) => $query->whereHas('pesanan', fn ($pesanan) => $pesanan->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereNotIn('status', ['dibatalkan']))], 'jumlah')
                    ->withSum(['itemPesanan as total_pendapatan_produk' => fn ($query) => $query->whereHas('pesanan', fn ($pesanan) => $pesanan->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar')))], 'subtotal')
                    ->orderBy('nama')
                    ->chunk(200, function ($products) use ($handle) {
                        foreach ($products as $product) {
                            fputcsv($handle, [$product->nama, $product->harga, $product->stok, $product->min_stok, $product->aktif ? 'Aktif' : 'Nonaktif', $product->total_terjual ?? 0, $product->total_pendapatan_produk ?? 0]);
                        }
                    });
            } elseif ($jenis === 'pembayaran') {
                fputcsv($handle, ['Invoice', 'Pembeli', 'Metode', 'Status', 'Referensi', 'Jumlah', 'Tanggal']);
                Pembayaran::query()->with('pesanan.user')->whereHas('pesanan', fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai]))
                    ->latest()->chunk(200, function ($payments) use ($handle) {
                        foreach ($payments as $payment) {
                            fputcsv($handle, [$payment->pesanan?->nomor_invoice, $payment->pesanan?->user?->name, $payment->metode_pembayaran, $payment->status, $payment->referensi_pembayaran, $payment->jumlah, optional($payment->created_at)->format('Y-m-d H:i')]);
                        }
                    });
            } elseif ($jenis === 'stok') {
                fputcsv($handle, ['Produk', 'Tipe', 'Perubahan', 'Catatan', 'Tanggal']);
                RiwayatStok::query()->with('produk')->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->latest()
                    ->chunk(200, function ($histories) use ($handle) {
                        foreach ($histories as $history) {
                            fputcsv($handle, [$history->produk?->nama, $history->tipe, $history->perubahan, $history->catatan, optional($history->created_at)->format('Y-m-d H:i')]);
                        }
                    });
            } else {
                fputcsv($handle, ['Invoice', 'Tanggal', 'Pembeli', 'Email', 'Telepon', 'Metode Ambil', 'Metode Bayar', 'Status Pesanan', 'Status Pembayaran', 'Jumlah Item', 'Subtotal Produk', 'Biaya Kirim', 'Total Bayar']);
                Pesanan::query()->with(['user', 'pembayaran', 'item'])->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])->latest('tanggal_pesanan')
                    ->chunk(200, function ($orders) use ($handle) {
                        foreach ($orders as $order) {
                            fputcsv($handle, [$order->nomor_invoice, optional($order->tanggal_pesanan)->format('Y-m-d H:i'), $order->user?->name, $order->user?->email, $order->user?->telepon, $order->metode_pengambilan, $order->pembayaran?->metode_pembayaran, $order->status, $order->pembayaran?->status ?? $order->status_pembayaran, $order->item->sum('jumlah'), $order->subtotal_produk, $order->biaya_pengiriman, $order->total_bayar]);
                        }
                    });
            }

            fclose($handle);
        }, $namaFile, ['Content-Type' => 'text/csv']);
    }


    public function exportExcel(Request $request): StreamedResponse
    {
        [$tanggalMulai, $tanggalSelesai] = $this->periode($request);
        $jenis = in_array($request->input('jenis'), $this->jenisLaporan, true) ? $request->input('jenis') : 'penjualan';
        $dataset = $this->exportDataset($request, $jenis, $tanggalMulai, $tanggalSelesai);
        $namaFile = 'laporan-' . $jenis . '-sitahu-' . $tanggalMulai->format('Ymd') . '-' . $tanggalSelesai->format('Ymd') . '.xls';

        return response()->streamDownload(function () use ($dataset, $tanggalMulai, $tanggalSelesai) {
            echo '<!doctype html><html><head><meta charset="UTF-8">';
            echo '<style>body{font-family:Arial,sans-serif;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #999;padding:6px 8px;font-size:12px;} th{background:#f3ead9;font-weight:bold;} .title{font-size:18px;font-weight:bold;margin-bottom:4px;} .period{font-size:12px;margin-bottom:14px;color:#555;} .total-box{margin:12px 0 14px;} .total-box td{border:1px solid #c89335;background:#fff8ea;font-weight:bold;}</style>';
            echo '</head><body>';
            echo '<div class="title">' . e($dataset['title']) . '</div>';
            echo '<div class="period">Periode: ' . e($tanggalMulai->format('d/m/Y')) . ' - ' . e($tanggalSelesai->format('d/m/Y')) . '</div>';
            if (! empty($dataset['totals'])) {
                echo '<table class="total-box"><tbody>';
                foreach ($dataset['totals'] as $total) {
                    echo '<tr><td>' . e($total['label'] ?? '') . '</td><td>' . e($total['value'] ?? '') . '</td></tr>';
                }
                echo '</tbody></table>';
            }
            echo '<table><thead><tr>';
            foreach ($dataset['headers'] as $header) {
                echo '<th>' . e($header) . '</th>';
            }
            echo '</tr></thead><tbody>';
            foreach ($dataset['rows'] as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . e((string) $cell) . '</td>';
                }
                echo '</tr>';
            }
            if (empty($dataset['rows'])) {
                echo '<tr><td colspan="' . max(1, count($dataset['headers'])) . '">Tidak ada data.</td></tr>';
            }
            echo '</tbody></table></body></html>';
        }, $namaFile, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request): View
    {
        [$tanggalMulai, $tanggalSelesai] = $this->periode($request);
        $jenis = in_array($request->input('jenis'), $this->jenisLaporan, true) ? $request->input('jenis') : 'penjualan';
        $dataset = $this->exportDataset($request, $jenis, $tanggalMulai, $tanggalSelesai);

        return view('admin.laporan.pdf', [
            'dataset' => $dataset,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'jenis' => $jenis,
        ]);
    }

    private function exportDataset(Request $request, string $jenis, Carbon $tanggalMulai, Carbon $tanggalSelesai): array
    {
        $q = trim((string) $request->input('q', ''));
        $statusFilter = $request->input('status');
        $metodeBayarFilter = $request->input('metode_pembayaran');
        $metodeAmbilFilter = $request->input('metode_pengambilan');

        $money = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $date = fn ($value) => $value ? Carbon::parse($value)->format('d/m/Y H:i') : '-';
        $statusLabel = fn ($value) => ucwords(str_replace('_', ' ', (string) $value));

        if ($jenis === 'pembeli') {
            $users = User::query()
                ->where('role', 'pembeli')
                ->when($q, fn ($query) => $query->where(fn ($sub) => $sub
                    ->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('telepon', 'like', '%' . $q . '%')))
                ->withCount(['pesanan as total_pesanan_periode' => fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])])
                ->withSum(['pesanan as total_belanja_periode' => fn ($query) => $query
                    ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
                    ->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar'))], 'total_bayar')
                ->orderBy('name')
                ->get();

            $rows = $users
                ->map(fn ($user) => [
                    $user->name,
                    $user->email,
                    $user->telepon ?: '-',
                    (string) $user->total_pesanan_periode,
                    $money($user->total_belanja_periode ?? 0),
                    $date($user->created_at),
                ])
                ->all();

            return [
                'title' => 'Laporan Akun Pembeli',
                'headers' => ['Nama Pembeli', 'Email', 'Telepon', 'Total Pesanan Periode', 'Total Belanja Periode', 'Tanggal Daftar'],
                'rows' => $rows,
                'totals' => [
                    ['label' => 'Total pembeli', 'value' => number_format($users->count())],
                    ['label' => 'Total pesanan periode', 'value' => number_format((int) $users->sum('total_pesanan_periode'))],
                    ['label' => 'Total belanja periode', 'value' => $money((float) $users->sum('total_belanja_periode'))],
                ],
            ];
        }

        if ($jenis === 'produk') {
            $products = Produk::query()
                ->when($q, fn ($query) => $query->where('nama', 'like', '%' . $q . '%'))
                ->withSum(['itemPesanan as total_terjual' => fn ($query) => $query->whereHas('pesanan', fn ($pesanan) => $pesanan
                    ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
                    ->whereNotIn('status', ['dibatalkan']))], 'jumlah')
                ->withSum(['itemPesanan as total_pendapatan_produk' => fn ($query) => $query->whereHas('pesanan', fn ($pesanan) => $pesanan
                    ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
                    ->whereHas('pembayaran', fn ($payment) => $payment->where('status', 'dibayar')))], 'subtotal')
                ->orderBy('nama')
                ->get();

            $rows = $products
                ->map(fn ($product) => [
                    $product->nama,
                    $money($product->harga),
                    (string) $product->stok,
                    (string) ($product->min_stok ?? 0),
                    $product->aktif ? 'Aktif' : 'Nonaktif',
                    (string) ($product->total_terjual ?? 0),
                    $money($product->total_pendapatan_produk ?? 0),
                ])
                ->all();

            return [
                'title' => 'Laporan Produk',
                'headers' => ['Produk', 'Harga', 'Stok', 'Minimal Stok', 'Status', 'Terjual Periode', 'Pendapatan Periode'],
                'rows' => $rows,
                'totals' => [
                    ['label' => 'Total produk', 'value' => number_format($products->count())],
                    ['label' => 'Total stok saat ini', 'value' => number_format((int) $products->sum('stok'))],
                    ['label' => 'Total terjual periode', 'value' => number_format((int) $products->sum('total_terjual'))],
                    ['label' => 'Total pendapatan produk', 'value' => $money((float) $products->sum('total_pendapatan_produk'))],
                ],
            ];
        }

        if ($jenis === 'pembayaran') {
            $payments = Pembayaran::query()
                ->with('pesanan.user')
                ->whereHas('pesanan', fn ($query) => $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai]))
                ->when($metodeBayarFilter, fn ($query) => $query->where('metode_pembayaran', $metodeBayarFilter))
                ->when($q, fn ($query) => $query->where(fn ($sub) => $sub
                    ->where('referensi_pembayaran', 'like', '%' . $q . '%')
                    ->orWhereHas('pesanan', fn ($pesanan) => $pesanan
                        ->where('nomor_invoice', 'like', '%' . $q . '%')
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%' . $q . '%')))))
                ->latest()
                ->get();

            $rows = $payments
                ->map(fn ($payment) => [
                    $payment->pesanan?->nomor_invoice ?: '-',
                    $payment->pesanan?->user?->name ?: '-',
                    $payment->metode_pembayaran === 'cod' ? 'COD' : 'Transfer Bank',
                    $statusLabel($payment->status),
                    $payment->referensi_pembayaran ?: '-',
                    $money($payment->jumlah),
                    $date($payment->created_at),
                ])
                ->all();

            return [
                'title' => 'Laporan Pembayaran',
                'headers' => ['Invoice', 'Pembeli', 'Metode', 'Status', 'Referensi', 'Jumlah', 'Tanggal'],
                'rows' => $rows,
                'totals' => [
                    ['label' => 'Total transaksi', 'value' => number_format($payments->count())],
                    ['label' => 'Total pembayaran berhasil', 'value' => $money((float) $payments->where('status', 'dibayar')->sum('jumlah'))],
                    ['label' => 'Total semua pembayaran', 'value' => $money((float) $payments->sum('jumlah'))],
                ],
            ];
        }

        if ($jenis === 'stok') {
            $histories = RiwayatStok::query()
                ->with('produk')
                ->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])
                ->when($q, fn ($query) => $query->whereHas('produk', fn ($produk) => $produk->where('nama', 'like', '%' . $q . '%')))
                ->latest()
                ->get();

            $histories = $this->hydrateMutasiStok($histories);

            $rows = $histories
                ->map(fn ($history) => [
                    $date($history->created_at),
                    $history->produk?->nama ?: '-',
                    $history->catatan ?: $statusLabel($history->tipe),
                    (string) number_format((int) ($history->masuk ?? 0)),
                    (string) number_format((int) ($history->keluar ?? 0)),
                    (string) number_format((int) ($history->sisa_stok ?? 0)),
                ])
                ->all();

            $sisaPerProduk = $histories
                ->groupBy('produk_id')
                ->map(fn ($rows) => (int) optional($rows->sortByDesc(fn ($row) => optional($row->created_at)->timestamp . '-' . str_pad((string) $row->id, 12, '0', STR_PAD_LEFT))->first())->sisa_stok)
                ->sum();

            return [
                'title' => 'Laporan Mutasi Stok',
                'headers' => ['Tanggal Transaksi', 'Produk', 'Uraian Mutasi', 'Tambah', 'Kurang', 'Sisa Stok'],
                'rows' => $rows,
                'totals' => [
                    ['label' => 'Total transaksi mutasi', 'value' => number_format($histories->count())],
                    ['label' => 'Total tambah', 'value' => number_format((int) $histories->sum('masuk'))],
                    ['label' => 'Total kurang', 'value' => number_format((int) $histories->sum('keluar'))],
                    ['label' => 'Sisa stok produk terkait', 'value' => number_format((int) $sisaPerProduk)],
                ],
            ];
        }

        $orders = Pesanan::query()
            ->with(['user', 'pembayaran', 'item'])
            ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_invoice', 'like', '%' . $q . '%')
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', '%' . $q . '%')
                                ->orWhere('email', 'like', '%' . $q . '%')
                                ->orWhere('telepon', 'like', '%' . $q . '%');
                        })
                        ->orWhereHas('item.produk', fn ($produkQuery) => $produkQuery->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
            ->when($metodeAmbilFilter, fn ($query) => $query->where('metode_pengambilan', $metodeAmbilFilter))
            ->when($metodeBayarFilter, fn ($query) => $query->whereHas('pembayaran', fn ($paymentQuery) => $paymentQuery->where('metode_pembayaran', $metodeBayarFilter)))
            ->latest('tanggal_pesanan')
            ->get();

        $rows = $orders
            ->map(fn ($order) => [
                $order->nomor_invoice,
                $date($order->tanggal_pesanan),
                $order->user?->name ?: '-',
                $order->user?->email ?: '-',
                $order->user?->telepon ?: '-',
                $order->metode_pengambilan === 'kurir_toko' ? 'Kurir Toko' : 'Ambil Toko',
                $order->pembayaran?->metode_pembayaran === 'cod' ? 'COD' : 'Transfer Bank',
                $statusLabel($order->status),
                $statusLabel($order->pembayaran?->status ?? $order->status_pembayaran),
                (string) $order->item->sum('jumlah'),
                $money($order->subtotal_produk),
                $money($order->biaya_pengiriman),
                $money($order->total_bayar),
            ])
            ->all();

        return [
            'title' => 'Laporan Penjualan',
            'headers' => ['Invoice', 'Tanggal', 'Pembeli', 'Email', 'Telepon', 'Metode Ambil', 'Metode Bayar', 'Status Pesanan', 'Status Pembayaran', 'Jumlah Item', 'Subtotal Produk', 'Biaya Kirim', 'Total Bayar'],
            'rows' => $rows,
            'totals' => [
                ['label' => 'Total invoice', 'value' => number_format($orders->count())],
                ['label' => 'Total item', 'value' => number_format((int) $orders->sum(fn ($order) => $order->item->sum('jumlah')))],
                ['label' => 'Total subtotal produk', 'value' => $money((float) $orders->sum('subtotal_produk'))],
                ['label' => 'Total pengiriman', 'value' => $money((float) $orders->sum('biaya_pengiriman'))],
                ['label' => 'Total bayar', 'value' => $money((float) $orders->sum('total_bayar'))],
            ],
        ];
    }

    private function hydrateMutasiStok($histories)
    {
        $histories = collect($histories);
        $produkIds = $histories->pluck('produk_id')->filter()->unique()->values();

        if ($produkIds->isEmpty()) {
            return $histories;
        }

        $stokSaatIni = Produk::query()
            ->whereIn('id', $produkIds)
            ->pluck('stok', 'id');

        $histories->groupBy('produk_id')->each(function ($rows, $produkId) use ($stokSaatIni) {
            $sorted = $rows->sortByDesc(function ($row) {
                return optional($row->created_at)->format('YmdHis') . '-' . str_pad((string) $row->id, 12, '0', STR_PAD_LEFT);
            });

            $latest = $sorted->first();
            $laterSum = 0;

            if ($latest) {
                $laterSum = (int) RiwayatStok::query()
                    ->where('produk_id', $produkId)
                    ->where(function ($query) use ($latest) {
                        $query->where('created_at', '>', $latest->created_at)
                            ->orWhere(function ($sameTime) use ($latest) {
                                $sameTime->where('created_at', $latest->created_at)
                                    ->where('id', '>', $latest->id);
                            });
                    })
                    ->sum('perubahan');
            }

            $saldo = (int) ($stokSaatIni[$produkId] ?? 0) - $laterSum;

            foreach ($sorted as $history) {
                $perubahan = (int) $history->perubahan;
                $history->masuk = max(0, $perubahan);
                $history->keluar = max(0, -$perubahan);
                $history->sisa_stok = $saldo;
                $saldo -= $perubahan;
            }
        });

        return $histories;
    }

    private function periode(Request $request): array
    {
        $tanggalMulai = $request->input('tanggal_mulai') ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay() : now()->startOfMonth();
        $tanggalSelesai = $request->input('tanggal_selesai') ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay() : now()->endOfDay();

        if ($tanggalMulai->greaterThan($tanggalSelesai)) {
            [$tanggalMulai, $tanggalSelesai] = [$tanggalSelesai->copy()->startOfDay(), $tanggalMulai->copy()->endOfDay()];
        }

        return [$tanggalMulai, $tanggalSelesai];
    }
}
