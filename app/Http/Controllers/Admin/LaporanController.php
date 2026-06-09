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

        $laporanHarian = collect();
        $cursor = $tanggalMulai->copy()->startOfDay();
        while ($cursor->lessThanOrEqualTo($tanggalSelesai)) {
            $tanggal = $cursor->copy();
            $laporanHarian->push([
                'tanggal' => $tanggal->format('d M'),
                'tanggal_lengkap' => $tanggal->format('d/m/Y'),
                'pesanan' => Pesanan::query()->whereDate('tanggal_pesanan', $tanggal)->count(),
                'pendapatan' => Pembayaran::query()
                    ->where('status', 'dibayar')
                    ->whereHas('pesanan', fn ($query) => $query->whereDate('tanggal_pesanan', $tanggal))
                    ->sum('jumlah'),
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
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            default => (clone $pesananFilter)->latest('tanggal_pesanan')->paginate(12)->withQueryString(),
        };

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
