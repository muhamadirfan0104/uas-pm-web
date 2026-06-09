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
            ->with('media')
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
            'foto_ulasan' => ['nullable', 'array', 'max:5'],
            'foto_ulasan.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video_ulasan' => ['nullable', 'array', 'max:2'],
            'video_ulasan.*' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:51200'],
        ], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'komentar.max' => 'Komentar maksimal 1000 karakter.',

            'foto_ulasan.array' => 'Foto ulasan harus dikirim sebagai daftar file.',
            'foto_ulasan.max' => 'Maksimal 5 foto ulasan dalam satu penilaian.',
            'foto_ulasan.*.image' => 'Setiap foto ulasan harus berupa gambar.',
            'foto_ulasan.*.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto_ulasan.*.max' => 'Ukuran setiap foto maksimal 4 MB.',

            'video_ulasan.array' => 'Video ulasan harus dikirim sebagai daftar file.',
            'video_ulasan.max' => 'Maksimal 2 video ulasan dalam satu penilaian.',
            'video_ulasan.*.file' => 'Setiap video ulasan harus berupa file video.',
            'video_ulasan.*.mimes' => 'Format video harus mp4, mov, avi, atau webm.',
            'video_ulasan.*.max' => 'Ukuran setiap video maksimal 50 MB.',
        ]);

        $ulasanLama = Ulasan::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('produk_id', $produk->id)
            ->where('user_id', Auth::id())
            ->first();

        $fotoLegacy = $ulasanLama?->foto_ulasan;
        $videoLegacy = $ulasanLama?->video_ulasan;

        $fotoFiles = $request->file('foto_ulasan', []);
        $videoFiles = $request->file('video_ulasan', []);

        if (! is_array($fotoFiles)) {
            $fotoFiles = [$fotoFiles];
        }

        if (! is_array($videoFiles)) {
            $videoFiles = [$videoFiles];
        }

        if (! $fotoLegacy && count(array_filter($fotoFiles)) > 0) {
            $fotoLegacy = $fotoFiles[0]->store('ulasan/foto', 'public');
            array_shift($fotoFiles);
        }

        if (! $videoLegacy && count(array_filter($videoFiles)) > 0) {
            $videoLegacy = $videoFiles[0]->store('ulasan/video', 'public');
            array_shift($videoFiles);
        }

        $ulasan = Ulasan::updateOrCreate(
            [
                'pesanan_id' => $pesanan->id,
                'produk_id' => $produk->id,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $data['rating'],
                'komentar' => $data['komentar'] ?? null,
                'foto_ulasan' => $fotoLegacy,
                'video_ulasan' => $videoLegacy,
                'ditampilkan' => true,
            ]
        );

        $urutan = (int) $ulasan->media()->max('urutan');

        foreach ($fotoFiles as $file) {
            if (! $file) {
                continue;
            }

            $ulasan->media()->create([
                'jenis' => 'foto',
                'path' => $file->store('ulasan/foto', 'public'),
                'urutan' => ++$urutan,
            ]);
        }

        foreach ($videoFiles as $file) {
            if (! $file) {
                continue;
            }

            $ulasan->media()->create([
                'jenis' => 'video',
                'path' => $file->store('ulasan/video', 'public'),
                'urutan' => ++$urutan,
            ]);
        }

        return redirect()
            ->route('pembeli-web.pesanan.show', $pesanan->nomor_invoice)
            ->with('success', 'Ulasan produk berhasil dikirim. Foto dan video akan tampil di detail produk.');
    }

    private function ambilPesananSelesai(string $nomor_invoice): Pesanan
    {
        $pesanan = Pesanan::query()
            ->with([
                'item.produk.gambarUtama',
                'ulasan.media',
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
