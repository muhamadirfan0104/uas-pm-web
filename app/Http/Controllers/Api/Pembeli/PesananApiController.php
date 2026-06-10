<?php

namespace App\Http\Controllers\Api\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\PengaturanToko;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\RiwayatStok;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PesananApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Pesanan::with([
                'item.produk.gambarUtama',
                'pembayaran',
                'pengiriman',
                'alamatPengiriman',
                'ulasan',
            ])
            ->where('user_id', $request->user()->id)
            ->latest('tanggal_pesanan')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function show(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);

        return response()->json([
            'success' => true,
            'data' => $pesanan->load([
                'item.produk.gambarUtama',
                'pembayaran',
                'pengiriman',
                'alamatPengiriman',
                'ulasan.media',
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.produk_id' => ['required', 'exists:produk,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'metode_pengambilan' => ['required', Rule::in(['ambil_toko', 'kurir_toko'])],
            'alamat_pengiriman_id' => ['nullable', 'exists:alamat,id'],
            'metode_pembayaran' => ['required', Rule::in(['cod', 'transfer_bank', 'tunai', 'qris'])],
        ]);

        $user = $request->user();
        $store = PengaturanToko::utama();
        $alamat = null;

        if ($data['metode_pengambilan'] === 'kurir_toko') {
            $alamat = Alamat::where('user_id', $user->id)
                ->findOrFail($data['alamat_pengiriman_id'] ?? 0);
        }

        $result = DB::transaction(function () use ($data, $user, $store, $alamat) {
            $subtotal = 0;
            $items = [];

            foreach ($data['items'] as $row) {
                $produk = Produk::where('aktif', true)
                    ->lockForUpdate()
                    ->findOrFail($row['produk_id']);

                $jumlah = (int) $row['jumlah'];

                if ($produk->stok < $jumlah) {
                    abort(422, 'Stok '.$produk->nama.' tidak mencukupi.');
                }

                $harga = (float) $produk->harga;
                $rowSubtotal = $harga * $jumlah;

                $subtotal += $rowSubtotal;

                $items[] = [
                    'produk' => $produk,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'subtotal' => $rowSubtotal,
                ];
            }

            $jarakKm = null;
            $ongkir = 0;

            if ($data['metode_pengambilan'] === 'kurir_toko') {
                $jarakKm = $this->hitungJarakKm(
                    (float) ($store->latitude_toko ?? 0),
                    (float) ($store->longitude_toko ?? 0),
                    (float) ($alamat->latitude ?? 0),
                    (float) ($alamat->longitude ?? 0)
                );

                $tarifPerKm = (float) ($store->tarif_per_km ?? 0);
                $biayaMinimum = (float) ($store->biaya_minimum_pengiriman ?? 0);
                $radiusMaksimal = (float) ($store->radius_maksimal_km ?? 0);

                if ($radiusMaksimal > 0 && $jarakKm > $radiusMaksimal) {
                    abort(422, 'Alamat berada di luar radius layanan kurir toko.');
                }

                $ongkirHitung = $jarakKm * $tarifPerKm;
                $ongkir = max($ongkirHitung, $biayaMinimum);
            }

            $total = $subtotal + $ongkir;
            $metodePembayaran = $data['metode_pembayaran'];

            $statusPesanan = in_array($metodePembayaran, ['cod', 'tunai'], true)
                ? 'menunggu_konfirmasi'
                : 'menunggu_pembayaran';

            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'nomor_invoice' => 'INV-'.now()->format('YmdHis').'-'.$user->id,
                'tanggal_pesanan' => Carbon::now(),
                'subtotal_produk' => $subtotal,
                'jarak_km' => $jarakKm,
                'biaya_pengiriman' => $ongkir,
                'total_bayar' => $total,
                'metode_pengambilan' => $data['metode_pengambilan'],
                'alamat_pengiriman_id' => $alamat?->id,
                'status' => $statusPesanan,
                'status_pembayaran' => 'menunggu_pembayaran',
            ]);

            foreach ($items as $item) {
                $pesanan->item()->create([
                    'produk_id' => $item['produk']->id,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);

                $item['produk']->decrement('stok', $item['jumlah']);

                RiwayatStok::create([
                    'produk_id' => $item['produk']->id,
                    'perubahan' => -$item['jumlah'],
                    'tipe' => 'kurang',
                    'catatan' => 'Stok berkurang karena checkout mobile pembeli invoice '.$pesanan->nomor_invoice,
                ]);
            }

            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode_pembayaran' => $metodePembayaran,
                'referensi_pembayaran' => strtoupper($metodePembayaran).'-'.now()->format('YmdHis').'-'.$pesanan->id,
                'jumlah' => $total,
                'status' => 'menunggu_pembayaran',
                'tautan_pembayaran' => null,
                'qr_code' => $metodePembayaran === 'qris'
                    ? 'QR-'.$pesanan->nomor_invoice
                    : null,
                'bukti_transfer' => null,
                'catatan_admin' => null,
            ]);

            Pengiriman::create([
                'pesanan_id' => $pesanan->id,
                'metode' => $data['metode_pengambilan'],
                'status_pengiriman' => null,
                'alamat_toko' => $store->alamat,
                'alamat_tujuan' => $alamat?->alamat_lengkap,
                'latitude_tujuan' => $alamat?->latitude,
                'longitude_tujuan' => $alamat?->longitude,
                'jarak_km' => $jarakKm,
                'biaya' => $ongkir,
            ]);

            return $pesanan->load([
                'item.produk.gambarUtama',
                'pembayaran',
                'pengiriman',
                'alamatPengiriman',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat.',
            'data' => $result,
        ], 201);
    }

    public function checkoutFromCart(Request $request): JsonResponse
    {
        $data = $request->validate([
            'metode_pengambilan' => ['required', Rule::in(['ambil_toko', 'kurir_toko'])],
            'alamat_pengiriman_id' => ['nullable', 'exists:alamat,id'],
            'metode_pembayaran' => ['required', Rule::in(['cod', 'transfer_bank', 'tunai', 'qris'])],
            'setuju_pesanan' => ['nullable'],
        ]);

        $user = $request->user();

        $keranjang = Keranjang::with(['item.produk'])
            ->where('user_id', $user->id)
            ->first();

        if (! $keranjang || $keranjang->item->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang masih kosong.',
            ], 422);
        }

        $items = $keranjang->item->map(fn ($item) => [
            'produk_id' => $item->produk_id,
            'jumlah' => $item->jumlah,
        ])->values()->all();

        $request->merge(['items' => $items]);

        $response = $this->store($request);

        if ($response->getStatusCode() === 201) {
            $keranjang->item()->delete();
        }

        return $response;
    }

    public function cancel(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);

        if (! in_array($pesanan->status, ['menunggu_pembayaran', 'menunggu_verifikasi', 'menunggu_konfirmasi'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak bisa dibatalkan karena sudah masuk proses toko.',
            ], 422);
        }

        if ($pesanan->status_pembayaran === 'dibayar') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan yang sudah dibayar tidak bisa dibatalkan dari aplikasi pembeli.',
            ], 422);
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->loadMissing(['item.produk', 'pembayaran', 'pengiriman']);

            foreach ($pesanan->item as $item) {
                if (! $item->produk) {
                    continue;
                }

                $item->produk->increment('stok', (int) $item->jumlah);

                RiwayatStok::create([
                    'produk_id' => $item->produk_id,
                    'perubahan' => (int) $item->jumlah,
                    'tipe' => 'tambah',
                    'catatan' => 'Stok dikembalikan karena pesanan dibatalkan pembeli invoice '.$pesanan->nomor_invoice,
                ]);
            }

            $pesanan->update([
                'status' => 'dibatalkan',
                'status_pembayaran' => 'dibatalkan',
            ]);

            $pesanan->pembayaran?->update([
                'status' => 'dibatalkan',
                'dibayar_pada' => null,
            ]);

            $pesanan->pengiriman?->update([
                'status_pengiriman' => null,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan.',
        ]);
    }

    public function confirmReceived(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);

        if (! in_array($pesanan->status, ['siap_diambil', 'dalam_pengantaran'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan belum bisa dikonfirmasi diterima.',
            ], 422);
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->loadMissing(['pembayaran', 'pengiriman']);

            $isBayarDiTempat = in_array(
                $pesanan->pembayaran?->metode_pembayaran,
                ['cod', 'tunai'],
                true
            );

            $pesanan->update([
                'status' => 'selesai',
                'status_pembayaran' => $isBayarDiTempat
                    ? 'dibayar'
                    : $pesanan->status_pembayaran,
            ]);

            $pesanan->pengiriman?->update([
                'status_pengiriman' => 'selesai',
            ]);

            if ($isBayarDiTempat && $pesanan->pembayaran) {
                $pesanan->pembayaran->update([
                    'status' => 'dibayar',
                    'dibayar_pada' => now(),
                    'diverifikasi_pada' => now(),
                    'catatan_admin' => $pesanan->pembayaran->catatan_admin
                        ?: 'Pembayaran COD/Tunai dikonfirmasi saat pembeli menerima pesanan.',
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dikonfirmasi diterima.',
        ]);
    }

    private function hitungJarakKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        if ($lat1 == 0.0 && $lon1 == 0.0) {
            return 0;
        }

        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}