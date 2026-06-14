<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\RiwayatStok;
use App\Support\OrderFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PesananApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        $query = Pesanan::with(['user', 'pembayaran', 'pengiriman'])
            ->latest('tanggal_pesanan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(15),
        ]);
    }

    public function show(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        return response()->json([
            'success' => true,
            'data' => $pesanan->load([
                'user',
                'item.produk',
                'pembayaran',
                'pengiriman',
                'alamatPengiriman',
            ]),
        ]);
    }

    public function updateStatus(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'menunggu_pembayaran',
                    'menunggu_verifikasi',
                    'diproses',
                    'disiapkan',
                    'siap_diambil',
                    'dalam_pengantaran',
                    'selesai',
                    'dibatalkan',
                ]),
            ],
        ]);

        try {
            DB::transaction(function () use ($pesanan, $data) {
                $pesanan->loadMissing(['item.produk', 'pembayaran', 'pengiriman']);

                $statusBaru = $data['status'];
                $statusLama = $pesanan->status;
                $metodeBayar = $pesanan->pembayaran?->metode_pembayaran;

                OrderFlow::assertOrderTransition($pesanan, $statusBaru);

                if ($statusBaru === 'dibatalkan' && $statusLama !== 'dibatalkan') {
                    foreach ($pesanan->item as $item) {
                        if (! $item->produk) {
                            continue;
                        }

                        $produk = $item->produk()->lockForUpdate()->first();
                        if (! $produk) {
                            continue;
                        }

                        $produk->increment('stok', (int) $item->jumlah);

                        RiwayatStok::create([
                            'produk_id' => $produk->id,
                            'perubahan' => (int) $item->jumlah,
                            'tipe' => 'tambah',
                            'catatan' => 'Stok dikembalikan karena admin API membatalkan invoice '.$pesanan->nomor_invoice,
                        ]);
                    }
                }

                $pesanan->status = $statusBaru;

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
                        'dibayar_pada' => $paymentStatus === 'dibayar'
                            ? now()
                            : $pesanan->pembayaran->dibayar_pada,
                        'diverifikasi_pada' => $paymentStatus === 'dibayar'
                            ? now()
                            : $pesanan->pembayaran->diverifikasi_pada,
                    ]);
                }

                if ($pesanan->pengiriman) {
                    $pesanan->pengiriman->update([
                        'status_pengiriman' => match ($statusBaru) {
                            'siap_diambil' => 'siap_diambil',
                            'dalam_pengantaran' => 'dalam_pengantaran',
                            'selesai' => 'selesai',
                            'dibatalkan' => null,
                            default => $pesanan->pengiriman->status_pengiriman,
                        },
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Status pesanan gagal diperbarui.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan diperbarui sesuai alur final.',
            'data' => $pesanan->fresh(['user', 'item.produk', 'pembayaran', 'pengiriman', 'alamatPengiriman']),
        ]);
    }
}
