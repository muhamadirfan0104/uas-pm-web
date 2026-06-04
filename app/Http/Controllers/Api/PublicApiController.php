<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\PengaturanToko;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'API TahuKu aktif.',
            'service' => 'Laravel API / Web Service',
        ]);
    }

    public function store(): JsonResponse
    {
        $store = PengaturanToko::utama();

        return response()->json(['success' => true, 'data' => $store]);
    }

    public function banners(): JsonResponse
    {
        $data = Banner::query()
            ->where('aktif', true)
            ->orderBy('urutan')
            ->latest()
            ->get()
            ->map(fn (Banner $banner) => [
                'id' => $banner->id,
                'judul' => $banner->judul,
                'deskripsi' => $banner->deskripsi,
                'url_gambar' => $banner->url_gambar ? asset('storage/'.$banner->url_gambar) : null,
            ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function products(Request $request): JsonResponse
    {
        $query = Produk::query()->with('gambarUtama')->where('aktif', true);

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->q.'%');
        }

        match ($request->get('sort')) {
            'harga_terendah' => $query->orderBy('harga'),
            'harga_tertinggi' => $query->orderByDesc('harga'),
            'terlaris' => $query->withSum('itemPesanan as total_terjual', 'jumlah')->orderByDesc('total_terjual'),
            default => $query->latest(),
        };

        $produk = $query->paginate((int) $request->get('per_page', 12));
        $produk->getCollection()->transform(fn (Produk $item) => $this->productPayload($item));

        return response()->json(['success' => true, 'data' => $produk]);
    }

    public function product(Produk $produk): JsonResponse
    {
        abort_unless($produk->aktif, 404);

        $produk->load(['gambar', 'gambarUtama']);

        return response()->json(['success' => true, 'data' => $this->productPayload($produk, true)]);
    }

    public function productReviews(Produk $produk): JsonResponse
    {
        $reviews = Ulasan::query()
            ->with('user:id,name')
            ->where('produk_id', $produk->id)
            ->where('ditampilkan', true)
            ->latest()
            ->paginate(10)
            ->through(fn (Ulasan $review) => [
                'id' => $review->id,
                'user' => $review->user?->name,
                'rating' => $review->rating,
                'komentar' => $review->komentar,
                'foto_ulasan' => $review->foto_ulasan ? asset('storage/'.$review->foto_ulasan) : null,
                'created_at' => $review->created_at,
            ]);

        return response()->json(['success' => true, 'data' => $reviews]);
    }

    private function productPayload(Produk $item, bool $detail = false): array
    {
        $payload = [
            'id' => $item->id,
            'nama' => $item->nama,
            'harga' => (float) $item->harga,
            'stok' => (int) $item->stok,
            'satuan' => $item->satuan,
            'isi_per_satuan' => $item->isi_per_satuan,
            'berat' => $item->berat,
            'gambar_utama' => $item->gambarUtama?->url_gambar ? asset('storage/'.$item->gambarUtama->url_gambar) : null,
            'aktif' => (bool) $item->aktif,
            'created_at' => $item->created_at,
        ];

        if ($detail) {
            $payload += [
                'deskripsi' => $item->deskripsi,
                'masa_simpan' => $item->masa_simpan,
                'saran_penyimpanan' => $item->saran_penyimpanan,
                'saran_penyajian' => $item->saran_penyajian,
                'gambar' => $item->gambar->map(fn ($img) => asset('storage/'.$img->url_gambar)),
            ];
        }

        return $payload;
    }
}
