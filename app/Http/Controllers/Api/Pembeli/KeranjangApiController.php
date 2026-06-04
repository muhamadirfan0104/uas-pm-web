<?php

namespace App\Http\Controllers\Api\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\ItemKeranjang;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeranjangApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $keranjang = $this->getKeranjang($request);

        $keranjang->load([
            'item.produk.gambarUtama',
        ]);

        $items = $keranjang->item->map(function ($item) {
            return [
                'id' => $item->id,
                'produk_id' => $item->produk_id,
                'nama_produk' => $item->produk?->nama,
                'harga_satuan' => (float) $item->harga_satuan,
                'jumlah' => (int) $item->jumlah,
                'subtotal' => (float) $item->subtotal,
                'stok_produk' => (int) ($item->produk?->stok ?? 0),
                'satuan' => $item->produk?->satuan,
                'gambar_utama' => $item->produk?->gambarUtama?->url_gambar
                    ? asset('storage/'.$item->produk->gambarUtama->url_gambar)
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data keranjang berhasil diambil.',
            'data' => [
                'keranjang_id' => $keranjang->id,
                'items' => $items,
                'total_item' => $keranjang->item->sum('jumlah'),
                'total_belanja' => (float) $keranjang->item->sum('subtotal'),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'produk_id' => ['required', 'exists:produk,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $keranjang = $this->getKeranjang($request);

        $produk = Produk::where('aktif', true)
            ->findOrFail($data['produk_id']);

        if ($produk->stok < $data['jumlah']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi.',
            ], 422);
        }

        $item = DB::transaction(function () use ($keranjang, $produk, $data) {
            $item = ItemKeranjang::where('keranjang_id', $keranjang->id)
                ->where('produk_id', $produk->id)
                ->first();

            if ($item) {
                $jumlahBaru = $item->jumlah + $data['jumlah'];

                if ($produk->stok < $jumlahBaru) {
                    abort(422, 'Jumlah keranjang melebihi stok produk.');
                }

                $item->update([
                    'jumlah' => $jumlahBaru,
                    'harga_satuan' => $produk->harga,
                    'subtotal' => $jumlahBaru * $produk->harga,
                ]);

                return $item;
            }

            return ItemKeranjang::create([
                'keranjang_id' => $keranjang->id,
                'produk_id' => $produk->id,
                'jumlah' => $data['jumlah'],
                'harga_satuan' => $produk->harga,
                'subtotal' => $data['jumlah'] * $produk->harga,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'data' => $item->load('produk.gambarUtama'),
        ], 201);
    }

    public function update(Request $request, ItemKeranjang $itemKeranjang): JsonResponse
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $this->authorizeItem($request, $itemKeranjang);

        $produk = $itemKeranjang->produk;

        if (! $produk || ! $produk->aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak tersedia.',
            ], 422);
        }

        if ($produk->stok < $data['jumlah']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi.',
            ], 422);
        }

        $itemKeranjang->update([
            'jumlah' => $data['jumlah'],
            'harga_satuan' => $produk->harga,
            'subtotal' => $data['jumlah'] * $produk->harga,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jumlah produk di keranjang berhasil diperbarui.',
            'data' => $itemKeranjang->fresh('produk.gambarUtama'),
        ]);
    }

    public function destroy(Request $request, ItemKeranjang $itemKeranjang): JsonResponse
    {
        $this->authorizeItem($request, $itemKeranjang);

        $itemKeranjang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang.',
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $keranjang = $this->getKeranjang($request);

        $keranjang->item()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan.',
        ]);
    }

    private function getKeranjang(Request $request): Keranjang
    {
        return Keranjang::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);
    }

    private function authorizeItem(Request $request, ItemKeranjang $itemKeranjang): void
    {
        abort_unless(
            $itemKeranjang->keranjang?->user_id === $request->user()->id,
            403,
            'Item keranjang tidak boleh diakses.'
        );
    }
}
