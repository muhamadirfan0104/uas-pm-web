<?php

namespace App\Http\Controllers\Api\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Ulasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UlasanApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pesanan_id' => ['required', 'exists:pesanan,id'],
            'produk_id' => ['required', 'exists:produk,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
            'foto_ulasan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video_ulasan' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:51200'],
        ]);

        $pesanan = Pesanan::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($data['pesanan_id']);

        if ($pesanan->status !== 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan hanya dapat dibuat setelah pesanan selesai.',
            ], 422);
        }

        if (! $pesanan->item()->where('produk_id', $data['produk_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ada pada pesanan ini.',
            ], 422);
        }

        $sudahAda = Ulasan::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('produk_id', $data['produk_id'])
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Produk ini sudah diberi ulasan untuk pesanan ini.',
            ], 422);
        }

        $fotoPath = null;
        $videoPath = null;

        if ($request->hasFile('foto_ulasan')) {
            $fotoPath = $request->file('foto_ulasan')->store('ulasan/foto', 'public');
        }

        if ($request->hasFile('video_ulasan')) {
            $videoPath = $request->file('video_ulasan')->store('ulasan/video', 'public');
        }

        $ulasan = Ulasan::create([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $data['produk_id'],
            'user_id' => $request->user()->id,
            'rating' => $data['rating'],
            'komentar' => $data['komentar'] ?? null,
            'foto_ulasan' => $fotoPath,
            'video_ulasan' => $videoPath,
            'ditampilkan' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dikirim.',
            'data' => $ulasan,
        ], 201);
    }

    public function myReviews(Request $request): JsonResponse
    {
        $reviews = Ulasan::query()
            ->with([
                'produk:id,nama',
                'pesanan:id,nomor_invoice,tanggal_pesanan',
            ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10)
            ->through(fn (Ulasan $review) => [
                'id' => $review->id,
                'pesanan_id' => $review->pesanan_id,
                'nomor_invoice' => $review->pesanan?->nomor_invoice,
                'tanggal_pesanan' => $review->pesanan?->tanggal_pesanan,
                'produk_id' => $review->produk_id,
                'nama_produk' => $review->produk?->nama ?: 'Produk',
                'rating' => (int) $review->rating,
                'komentar' => $review->komentar,
                'foto_ulasan' => $this->mediaUrl($review->foto_ulasan),
                'video_ulasan' => $this->mediaUrl($review->video_ulasan),
                'created_at' => $review->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    private function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url('storage/'.ltrim($path, '/'));
    }
}