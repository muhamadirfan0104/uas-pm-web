<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemPesanan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $tanggalMulai = $request->input('tanggal_mulai')
            ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay()
            : now()->startOfMonth();

        $tanggalSelesai = $request->input('tanggal_selesai')
            ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay()
            : now()->endOfDay();

        if ($tanggalMulai->greaterThan($tanggalSelesai)) {
            [$tanggalMulai, $tanggalSelesai] = [$tanggalSelesai->copy()->startOfDay(), $tanggalMulai->copy()->endOfDay()];
        }

        $pesananQuery = Pesanan::query()
            ->with(['user', 'pembayaran', 'pengiriman', 'item.produk'])
            ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai]);

        $pembayaranDibayarQuery = Pembayaran::query()
            ->where('status', 'dibayar')
            ->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);

        $pesanan = (clone $pesananQuery)
            ->latest('tanggal_pesanan')
            ->paginate(12)
            ->withQueryString();

        $totalPesanan = (clone $pesananQuery)->count();

        $pesananSelesai = (clone $pesananQuery)
            ->where('status', 'selesai')
            ->count();

        $pesananDibatalkan = (clone $pesananQuery)
            ->where('status', 'dibatalkan')
            ->count();

        $totalPendapatan = (clone $pembayaranDibayarQuery)
            ->sum('jumlah');

        $totalProdukTerjual = ItemPesanan::query()
            ->whereHas('pesanan', function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
                    ->whereNotIn('status', ['dibatalkan']);
            })
            ->sum('jumlah');

        $produkTerlaris = Produk::query()
            ->withSum([
                'itemPesanan as total_terjual' => function ($query) use ($tanggalMulai, $tanggalSelesai) {
                    $query->whereHas('pesanan', function ($pesananQuery) use ($tanggalMulai, $tanggalSelesai) {
                        $pesananQuery->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
                            ->whereNotIn('status', ['dibatalkan']);
                    });
                },
            ], 'jumlah')
            ->withSum([
                'itemPesanan as total_pendapatan_produk' => function ($query) use ($tanggalMulai, $tanggalSelesai) {
                    $query->whereHas('pesanan', function ($pesananQuery) use ($tanggalMulai, $tanggalSelesai) {
                        $pesananQuery->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
                            ->whereNotIn('status', ['dibatalkan']);
                    });
                },
            ], 'subtotal')
            ->orderByDesc('total_terjual')
            ->limit(8)
            ->get();

        $statusPesanan = (clone $pesananQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusPembayaran = Pembayaran::query()
            ->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stokMenipis = Produk::query()
            ->where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'min_stok')
            ->count();

        $stokHabis = Produk::query()
            ->where('stok', '<=', 0)
            ->count();

        $produkStokPerhatian = Produk::query()
            ->where(function ($query) {
                $query->where('stok', '<=', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('stok', '>', 0)
                            ->whereColumn('stok', '<=', 'min_stok');
                    });
            })
            ->orderBy('stok')
            ->limit(8)
            ->get();

        $laporanHarian = collect();

        $cursor = $tanggalMulai->copy()->startOfDay();

        while ($cursor->lessThanOrEqualTo($tanggalSelesai)) {
            $tanggal = $cursor->copy();

            $laporanHarian->push([
                'tanggal' => $tanggal->format('d/m/Y'),
                'pesanan' => Pesanan::query()
                    ->whereDate('tanggal_pesanan', $tanggal)
                    ->count(),
                'pendapatan' => Pembayaran::query()
                    ->where('status', 'dibayar')
                    ->whereDate('created_at', $tanggal)
                    ->sum('jumlah'),
            ]);

            $cursor->addDay();
        }

        $stats = [
            'total_pendapatan' => $totalPendapatan,
            'total_pesanan' => $totalPesanan,
            'pesanan_selesai' => $pesananSelesai,
            'pesanan_dibatalkan' => $pesananDibatalkan,
            'total_produk_terjual' => $totalProdukTerjual,
            'stok_menipis' => $stokMenipis,
            'stok_habis' => $stokHabis,
        ];

        return view('admin.laporan.index', compact(
            'tanggalMulai',
            'tanggalSelesai',
            'pesanan',
            'produkTerlaris',
            'statusPesanan',
            'statusPembayaran',
            'produkStokPerhatian',
            'laporanHarian',
            'stats'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $tanggalMulai = $request->input('tanggal_mulai')
            ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay()
            : now()->startOfMonth();

        $tanggalSelesai = $request->input('tanggal_selesai')
            ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay()
            : now()->endOfDay();

        if ($tanggalMulai->greaterThan($tanggalSelesai)) {
            [$tanggalMulai, $tanggalSelesai] = [$tanggalSelesai->copy()->startOfDay(), $tanggalMulai->copy()->endOfDay()];
        }

        $namaFile = 'laporan-si-tahu-' . $tanggalMulai->format('Ymd') . '-' . $tanggalSelesai->format('Ymd') . '.csv';

        $pesanan = Pesanan::query()
            ->with(['user', 'pembayaran', 'pengiriman', 'item.produk'])
            ->whereBetween('tanggal_pesanan', [$tanggalMulai, $tanggalSelesai])
            ->latest('tanggal_pesanan')
            ->get();

        return response()->streamDownload(function () use ($pesanan) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Nomor Invoice',
                'Tanggal Pesanan',
                'Nama Pembeli',
                'Email Pembeli',
                'Telepon',
                'Metode Pengambilan',
                'Status Pesanan',
                'Status Pembayaran',
                'Metode Pembayaran',
                'Jumlah Item',
                'Subtotal Produk',
                'Biaya Pengiriman',
                'Total Bayar',
            ]);

            foreach ($pesanan as $order) {
                fputcsv($handle, [
                    $order->nomor_invoice,
                    optional($order->tanggal_pesanan)->format('d/m/Y H:i'),
                    $order->user?->name ?? '-',
                    $order->user?->email ?? '-',
                    $order->user?->telepon ?? '-',
                    $order->metode_pengambilan,
                    $order->status,
                    $order->status_pembayaran,
                    $order->pembayaran?->metode_pembayaran ?? '-',
                    $order->item->sum('jumlah'),
                    $order->subtotal_produk,
                    $order->biaya_pengiriman,
                    $order->total_bayar,
                ]);
            }

            fclose($handle);
        }, $namaFile, [
            'Content-Type' => 'text/csv',
        ]);
    }
}