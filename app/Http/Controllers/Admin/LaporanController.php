<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $tanggalMulai = $request->date('tanggal_mulai') ?? Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->date('tanggal_selesai') ?? Carbon::now();
        $jenis = $request->input('jenis', 'penjualan');

        $pesananQuery = Pesanan::with('user')
            ->whereBetween('tanggal_pesanan', [$tanggalMulai->startOfDay(), $tanggalSelesai->endOfDay()]);

        if ($request->filled('q')) {
            $search = $request->q;
            $pesananQuery->where(function ($query) use ($search) {
                $query->where('nomor_invoice', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $pesananQuery->where('status', $request->status);
        }

        $pesanan = $pesananQuery->latest('tanggal_pesanan')->paginate(10)->withQueryString();

        $ringkasan = [
            'total_penjualan' => Pembayaran::where('status', 'dibayar')
                ->whereBetween('created_at', [$tanggalMulai->startOfDay(), $tanggalSelesai->endOfDay()])
                ->sum('jumlah'),
            'total_pesanan' => (clone $pesananQuery)->count(),
            'pesanan_selesai' => (clone $pesananQuery)->where('status', 'selesai')->count(),
            'pesanan_dibatalkan' => (clone $pesananQuery)->where('status', 'dibatalkan')->count(),
        ];

        $produkTerlaris = Produk::withSum(['itemPesanan as total_terjual' => function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereHas('pesanan', fn ($q) => $q->whereBetween('tanggal_pesanan', [$tanggalMulai->startOfDay(), $tanggalSelesai->endOfDay()]));
            }], 'jumlah')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return view('admin.laporan.index', compact('jenis', 'tanggalMulai', 'tanggalSelesai', 'pesanan', 'ringkasan', 'produkTerlaris'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $tanggalMulai = $request->date('tanggal_mulai') ?? Carbon::now()->startOfMonth();
        $tanggalSelesai = $request->date('tanggal_selesai') ?? Carbon::now();

        $filename = 'laporan-penjualan-' . now()->format('Ymd-His') . '.csv';

        $callback = function () use ($tanggalMulai, $tanggalSelesai) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice', 'Pembeli', 'Tanggal', 'Status', 'Status Pembayaran', 'Total Bayar']);

            Pesanan::with('user')
                ->whereBetween('tanggal_pesanan', [$tanggalMulai->startOfDay(), $tanggalSelesai->endOfDay()])
                ->orderBy('tanggal_pesanan')
                ->chunk(200, function ($orders) use ($handle) {
                    foreach ($orders as $order) {
                        fputcsv($handle, [
                            $order->nomor_invoice,
                            $order->user?->name,
                            optional($order->tanggal_pesanan)->format('Y-m-d H:i:s'),
                            $order->status,
                            $order->status_pembayaran,
                            $order->total_bayar,
                        ]);
                    }
                });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
