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
            'komentar' => ['nullable', 'string'],
            'foto_ulasan' => ['nullable', 'image', 'max:4096'],
        ]);

        $pesanan = Pesanan::where('user_id', $request->user()->id)->findOrFail($data['pesanan_id']);
        abort_unless($pesanan->status === 'selesai', 422, 'Ulasan hanya dapat dibuat setelah pesanan selesai.');
        abort_unless($pesanan->item()->where('produk_id', $data['produk_id'])->exists(), 422, 'Produk tidak ada pada pesanan ini.');

        $path = $request->hasFile('foto_ulasan') ? $request->file('foto_ulasan')->store('ulasan', 'public') : null;

        $ulasan = Ulasan::updateOrCreate([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $data['produk_id'],
            'user_id' => $request->user()->id,
        ], [
            'rating' => $data['rating'],
            'komentar' => $data['komentar'] ?? null,
            'foto_ulasan' => $path,
            'ditampilkan' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Ulasan berhasil dikirim.', 'data' => $ulasan], 201);
    }
}
