<?php

namespace App\Http\Controllers\Api\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Pembayaran;
use App\Models\PengaturanToko;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\Produk;
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
