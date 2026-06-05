<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UlasanController extends Controller
{
    public function create(string $nomor_invoice, Produk $produk): View
    {
        $pesanan = $this->ambilPesananSelesai($nomor_invoice);

        abort_unless(
            $pesanan->item()->where('produk_id', $produk->id)->exists(),
            404
        );

        $ulasan = Ulasan::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('produk_id', $produk->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('pembeli.ulasan-form', compact('pesanan', 'produk', 'ulasan'));
    }

    public function store(Request $request, string $nomor_invoice, Produk $produk): RedirectResponse
    {
        $pesanan = $this->ambilPesananSelesai($nomor_invoice);

        abort_unless(
            $pesanan->item()->where('produk_id', $produk->id)->exists(),
            404
        );

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
            'foto_ulasan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video_ulasan' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:51200'],
        ], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'komentar.max' => 'Komentar maksimal 1000 karakter.',

            'foto_ulasan.image' => 'Foto ulasan harus berupa gambar.',
            'foto_ulasan.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto_ulasan.max' => 'Ukuran foto maksimal 4 MB.',

            'video_ulasan.file' => 'Video ulasan harus berupa file video.',
            'video_ulasan.mimes' => 'Format video harus mp4, mov, avi, atau webm.',
            'video_ulasan.max' => 'Ukuran video maksimal 50 MB.',
        ]);

        $ulasan = Ulasan::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('produk_id', $produk->id)
            ->where('user_id', Auth::id())
            ->first();

        $fotoPath = $ulasan?->foto_ulasan;
        $videoPath = $ulasan?->video_ulasan;

        if ($request->hasFile('foto_ulasan')) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }

            $fotoPath = $request->file('foto_ulasan')->store('ulasan/foto', 'public');
        }

        if ($request->hasFile('video_ulasan')) {
            if ($videoPath) {
                Storage::disk('public')->delete($videoPath);
            }

            $videoPath = $request->file('video_ulasan')->store('ulasan/video', 'public');
        }

        Ulasan::updateOrCreate(
            [
                'pesanan_id' => $pesanan->id,
                'produk_id' => $produk->id,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $data['rating'],
                'komentar' => $data['komentar'] ?? null,
                'foto_ulasan' => $fotoPath,
                'video_ulasan' => $videoPath,
                'ditampilkan' => true,
            ]
        );

        return redirect()
            ->route('pembeli-web.pesanan.show', $pesanan->nomor_invoice)
            ->with('success', 'Ulasan produk berhasil dikirim.');
    }

    private function ambilPesananSelesai(string $nomor_invoice): Pesanan
    {
        $pesanan = Pesanan::query()
            ->with([
                'item.produk.gambarUtama',
                'ulasan',
            ])
            ->where('user_id', Auth::id())
            ->where('nomor_invoice', $nomor_invoice)
            ->firstOrFail();

        abort_unless(
            $pesanan->status === 'selesai',
            403,
            'Ulasan hanya bisa diberikan setelah pesanan selesai.'
        );

        return $pesanan;
    }
}