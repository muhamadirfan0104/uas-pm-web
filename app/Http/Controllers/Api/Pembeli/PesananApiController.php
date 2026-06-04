<?php

namespace App\Http\Controllers\Api\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Pembayaran;
use App\Models\PengaturanToko;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Keranjang;
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
        $orders = Pesanan::with(['item.produk', 'pembayaran', 'pengiriman'])
            ->where('user_id', $request->user()->id)
            ->latest('tanggal_pesanan')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function show(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);

        return response()->json(['success' => true, 'data' => $pesanan->load(['item.produk', 'pembayaran', 'pengiriman', 'alamatPengiriman'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.produk_id' => ['required', 'exists:produk,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'metode_pengambilan' => ['required', Rule::in(['ambil_toko', 'kurir_toko'])],
            'alamat_pengiriman_id' => ['nullable', 'exists:alamat,id'],
            'metode_pembayaran' => ['required', Rule::in(['qris', 'va', 'ewallet'])],
        ]);

        $user = $request->user();
        $store = PengaturanToko::utama();
        $alamat = null;

        if ($data['metode_pengambilan'] === 'kurir_toko') {
            $alamat = Alamat::where('user_id', $user->id)->findOrFail($data['alamat_pengiriman_id'] ?? 0);
        }

        $result = DB::transaction(function () use ($data, $user, $store, $alamat) {
            $subtotal = 0;
            $items = [];

            foreach ($data['items'] as $row) {
                $produk = Produk::where('aktif', true)->lockForUpdate()->findOrFail($row['produk_id']);

                if ($produk->stok < $row['jumlah']) {
                    abort(422, 'Stok '.$produk->nama.' tidak mencukupi.');
                }

                $rowSubtotal = (float) $produk->harga * (int) $row['jumlah'];
                $subtotal += $rowSubtotal;
                $items[] = compact('produk', 'row', 'rowSubtotal');
            }

            $ongkir = $data['metode_pengambilan'] === 'kurir_toko' ? (float) ($store->biaya_minimum_pengiriman ?? 0) : 0;
            $total = $subtotal + $ongkir;

            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'nomor_invoice' => 'INV-'.now()->format('YmdHis').'-'.$user->id,
                'tanggal_pesanan' => Carbon::now(),
                'subtotal_produk' => $subtotal,
                'biaya_pengiriman' => $ongkir,
                'total_bayar' => $total,
                'metode_pengambilan' => $data['metode_pengambilan'],
                'alamat_pengiriman_id' => $alamat?->id,
                'status' => 'menunggu_pembayaran',
                'status_pembayaran' => 'menunggu_pembayaran',
            ]);

            foreach ($items as $item) {
                $pesanan->item()->create([
                    'produk_id' => $item['produk']->id,
                    'jumlah' => $item['row']['jumlah'],
                    'harga_satuan' => $item['produk']->harga,
                    'subtotal' => $item['rowSubtotal'],
                ]);
            }

            $pembayaran = Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode_pembayaran' => $data['metode_pembayaran'],
                'referensi_pembayaran' => 'PAY-'.strtoupper($data['metode_pembayaran']).'-'.now()->format('His').$pesanan->id,
                'jumlah' => $total,
                'status' => 'menunggu_pembayaran',
                'tautan_pembayaran' => url('/api/payments/'.$pesanan->id),
                'qr_code' => $data['metode_pembayaran'] === 'qris' ? 'QR-'.$pesanan->nomor_invoice : null,
            ]);

            Pengiriman::create([
                'pesanan_id' => $pesanan->id,
                'metode' => $data['metode_pengambilan'],
                'alamat_toko' => $store->alamat,
                'alamat_tujuan' => $alamat?->alamat_lengkap,
                'latitude_tujuan' => $alamat?->latitude,
                'longitude_tujuan' => $alamat?->longitude,
                'biaya' => $ongkir,
            ]);

            return $pesanan->load(['item.produk', 'pembayaran', 'pengiriman']);
        });

        return response()->json(['success' => true, 'message' => 'Pesanan berhasil dibuat.', 'data' => $result], 201);
    }

    public function checkoutFromCart(Request $request): JsonResponse
    {
        $data = $request->validate([
            'metode_pengambilan' => ['required', Rule::in(['ambil_toko', 'kurir_toko'])],
            'alamat_pengiriman_id' => ['nullable', 'exists:alamat,id'],
            'metode_pembayaran' => ['required', Rule::in(['qris', 'va', 'ewallet'])],
            'setuju_pesanan' => ['required', 'accepted'],
        ]);

        $user = $request->user();
        $store = PengaturanToko::utama();

        $keranjang = Keranjang::with(['item.produk'])
            ->where('user_id', $user->id)
            ->first();

        if (! $keranjang || $keranjang->item->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang masih kosong.',
            ], 422);
        }

        $alamat = null;

        if ($data['metode_pengambilan'] === 'kurir_toko') {
            $alamat = Alamat::where('user_id', $user->id)
                ->findOrFail($data['alamat_pengiriman_id'] ?? 0);

            if (! $alamat->latitude || ! $alamat->longitude) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat belum memiliki titik lokasi maps.',
                ], 422);
            }
        }

        $result = DB::transaction(function () use ($data, $user, $store, $keranjang, $alamat) {
            $subtotal = 0;
            $checkoutItems = [];

            foreach ($keranjang->item as $itemKeranjang) {
                $produk = Produk::where('aktif', true)
                    ->lockForUpdate()
                    ->findOrFail($itemKeranjang->produk_id);

                if ($produk->stok < $itemKeranjang->jumlah) {
                    abort(422, 'Stok '.$produk->nama.' tidak mencukupi.');
                }

                $rowSubtotal = (float) $produk->harga * (int) $itemKeranjang->jumlah;
                $subtotal += $rowSubtotal;

                $checkoutItems[] = [
                    'produk' => $produk,
                    'jumlah' => (int) $itemKeranjang->jumlah,
                    'harga_satuan' => (float) $produk->harga,
                    'subtotal' => $rowSubtotal,
                ];
            }

            $jarakKm = null;
            $ongkir = 0;

            if ($data['metode_pengambilan'] === 'kurir_toko') {
                $jarakKm = $this->hitungJarakKm(
                    (float) ($store->latitude_toko ?? 0),
                    (float) ($store->longitude_toko ?? 0),
                    (float) $alamat->latitude,
                    (float) $alamat->longitude
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
                'status' => 'menunggu_pembayaran',
                'status_pembayaran' => 'menunggu_pembayaran',
            ]);

            foreach ($checkoutItems as $checkoutItem) {
                $pesanan->item()->create([
                    'produk_id' => $checkoutItem['produk']->id,
                    'jumlah' => $checkoutItem['jumlah'],
                    'harga_satuan' => $checkoutItem['harga_satuan'],
                    'subtotal' => $checkoutItem['subtotal'],
                ]);

                $checkoutItem['produk']->decrement('stok', $checkoutItem['jumlah']);

                RiwayatStok::create([
                    'produk_id' => $checkoutItem['produk']->id,
                    'perubahan' => -$checkoutItem['jumlah'],
                    'tipe' => 'kurang',
                    'catatan' => 'Stok berkurang karena checkout pesanan '.$pesanan->nomor_invoice,
                ]);
            }

            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode_pembayaran' => $data['metode_pembayaran'],
                'referensi_pembayaran' => 'PAY-'.strtoupper($data['metode_pembayaran']).'-'.now()->format('His').$pesanan->id,
                'jumlah' => $total,
                'status' => 'menunggu_pembayaran',
                'tautan_pembayaran' => url('/api/payments/'.$pesanan->id),
                'qr_code' => $data['metode_pembayaran'] === 'qris' ? 'QR-'.$pesanan->nomor_invoice : null,
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

            $keranjang->item()->delete();

            return $pesanan->load(['item.produk.gambarUtama', 'pembayaran', 'pengiriman', 'alamatPengiriman']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil. Pesanan berhasil dibuat.',
            'data' => $result,
        ], 201);
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

    public function cancel(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);
        abort_if($pesanan->status_pembayaran === 'dibayar', 422, 'Pesanan yang sudah dibayar tidak bisa dibatalkan dari aplikasi pembeli.');

        $pesanan->update(['status' => 'dibatalkan', 'status_pembayaran' => 'dibatalkan']);
        $pesanan->pembayaran?->update(['status' => 'dibatalkan']);

        return response()->json(['success' => true, 'message' => 'Pesanan berhasil dibatalkan.']);
    }

    public function confirmReceived(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);
        $pesanan->update(['status' => 'selesai']);
        $pesanan->pengiriman?->update(['status_pengiriman' => 'selesai']);

        return response()->json(['success' => true, 'message' => 'Pesanan diterima.']);
    }
}
