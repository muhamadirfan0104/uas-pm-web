<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\RiwayatStok;
use App\Support\OrderFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pesanan::with(['user', 'pembayaran', 'pengiriman', 'alamatPengiriman', 'item.produk.gambarUtama'])
            ->latest('tanggal_pesanan');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_invoice', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($user) use ($keyword) {
                        $user->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%')
                            ->orWhere('telepon', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('item.produk', fn ($produk) => $produk->where('nama', 'like', '%' . $keyword . '%'));
            });
        }

        $tab = (string) $request->input('tab', 'semua');
        if (! $request->filled('status') && $tab !== 'semua') {
            match ($tab) {
                'verifikasi' => $query->whereHas('pembayaran', function ($pay) {
                    $pay->where('metode_pembayaran', 'transfer_bank')
                        ->where('status', 'menunggu_pembayaran')
                        ->whereNotNull('bukti_transfer')
                        ->where('bukti_transfer', '!=', '');
                }),
                'belum_bayar' => $query->where('status', 'menunggu_pembayaran'),
                'diproses' => $query->where('status', 'diproses'),
                'ambil_kirim' => $query->whereIn('status', ['siap_diambil', 'dalam_pengantaran']),
                'selesai' => $query->where('status', 'selesai'),
                'dibatalkan' => $query->where('status', 'dibatalkan'),
                default => null,
            };
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->filled('metode_pengambilan')) {
            $query->where('metode_pengambilan', $request->metode_pengambilan);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->whereHas('pembayaran', fn ($pay) => $pay->where('metode_pembayaran', $request->metode_pembayaran));
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_pesanan', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_pesanan', '<=', $request->tanggal_selesai);
        }

        $pesanan = $query->paginate(10)->withQueryString();

        $statusCounts = Pesanan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'baru' => Pesanan::where('status', 'menunggu_pembayaran')->count(),
            'diproses' => Pesanan::where('status', 'diproses')->count(),
            'ambil_kirim' => Pesanan::whereIn('status', ['siap_diambil', 'dalam_pengantaran'])->count(),
            'selesai' => Pesanan::where('status', 'selesai')->count(),
            'perlu_verifikasi' => Pesanan::whereHas('pembayaran', function ($pay) {
                $pay->where('metode_pembayaran', 'transfer_bank')
                    ->where('status', 'menunggu_pembayaran')
                    ->whereNotNull('bukti_transfer')
                    ->where('bukti_transfer', '!=', '');
            })->count(),
        ];

        return view('admin.pesanan.index', compact('pesanan', 'statusCounts', 'stats'));
    }

    public function show(Pesanan $pesanan): View
    {
        $pesanan->load([
            'user',
            'alamatPengiriman',
            'item.produk.gambarUtama',
            'pembayaran',
            'pengiriman',
            'ulasan',
        ]);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function invoice(Pesanan $pesanan): View
    {
        $pesanan->load([
            'user',
            'alamatPengiriman',
            'item.produk',
            'pembayaran',
            'pengiriman',
        ]);

        return view('admin.pesanan.invoice', compact('pesanan'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:menunggu_pembayaran,dibayar,diproses,siap_diambil,dalam_pengantaran,selesai,dibatalkan'],
            'status_pembayaran' => ['nullable', 'in:menunggu_pembayaran,dibayar,ditolak,gagal,kedaluwarsa,dibatalkan'],
        ]);

        try {
            DB::transaction(function () use ($pesanan, $data) {
                $pesanan->loadMissing(['pembayaran', 'pengiriman', 'item.produk']);

                $statusBaru = $data['status'];
                $statusLama = $pesanan->status;
                $metodeBayar = $pesanan->pembayaran?->metode_pembayaran;

                OrderFlow::assertOrderTransition($pesanan, $statusBaru);

                if ($statusBaru === 'dibatalkan' && $statusLama !== 'dibatalkan') {
                    foreach ($pesanan->item as $item) {
                        if ($item->produk) {
                            $item->produk->increment('stok', (int) $item->jumlah);

                            RiwayatStok::create([
                                'produk_id' => $item->produk->id,
                                'perubahan' => (int) $item->jumlah,
                                'tipe' => 'tambah',
                                'catatan' => 'Stok dikembalikan karena admin membatalkan invoice ' . $pesanan->nomor_invoice,
                            ]);
                        }
                    }
                }

                $pesanan->status = $statusBaru;

                if (isset($data['status_pembayaran'])) {
                    $pesanan->status_pembayaran = $data['status_pembayaran'];
                }

                if ($statusBaru === 'dibatalkan') {
                    $pesanan->status_pembayaran = 'dibatalkan';
                }

                if ($statusBaru === 'selesai' && $metodeBayar === 'cod') {
                    $pesanan->status_pembayaran = 'dibayar';
                }

                $pesanan->save();

                if ($pesanan->pembayaran) {
                    $paymentStatus = $pesanan->status_pembayaran;
                    $pesanan->pembayaran->update([
                        'status' => $paymentStatus,
                        'dibayar_pada' => $paymentStatus === 'dibayar' ? now() : $pesanan->pembayaran->dibayar_pada,
                        'diverifikasi_pada' => $paymentStatus === 'dibayar' ? now() : $pesanan->pembayaran->diverifikasi_pada,
                    ]);
                }

                if ($pesanan->pengiriman) {
                    $statusPengiriman = match ($statusBaru) {
                        'siap_diambil' => 'siap_diambil',
                        'dalam_pengantaran' => 'dalam_pengantaran',
                        'selesai' => 'selesai',
                        'dibatalkan' => null,
                        default => $pesanan->pengiriman->status_pengiriman,
                    };

                    $pesanan->pengiriman->update([
                        'status_pengiriman' => $statusPengiriman,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Status pesanan gagal diperbarui.');
        }

        return back()->with('success', 'Status pesanan berhasil mengikuti alur operasional.');
    }
}
