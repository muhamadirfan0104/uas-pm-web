<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    public function index(): View
    {
        $dataKeranjang = $this->ambilDataKeranjang();

        return view('pembeli.keranjang', [
            'items' => $dataKeranjang['items'],
            'totalItem' => $dataKeranjang['totalItem'],
            'totalBelanja' => $dataKeranjang['totalBelanja'],
        ]);
    }

    public function store(Request $request, Produk $produk): RedirectResponse
    {
        if (! $produk->aktif) {
            return back()->with('error', 'Produk belum tersedia.');
        }

        if ((int) $produk->stok <= 0) {
            return back()->with('error', 'Stok produk sedang habis.');
        }

        $request->validate([
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $jumlahBaru = max(1, (int) $request->input('jumlah', 1));
        $keranjang = session('keranjang_web', []);

        $produkId = (string) $produk->id;
        $jumlahLama = isset($keranjang[$produkId])
            ? (int) $keranjang[$produkId]['jumlah']
            : 0;

        $jumlahAkhir = min($jumlahLama + $jumlahBaru, (int) $produk->stok);

        $keranjang[$produkId] = [
            'produk_id' => $produk->id,
            'jumlah' => $jumlahAkhir,
        ];

        session(['keranjang_web' => $keranjang]);

        return redirect()
            ->route('pembeli-web.keranjang.index')
            ->with('success', $produk->nama . ' berhasil masuk keranjang.');
    }

    public function update(Request $request, Produk $produk): JsonResponse|RedirectResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:tambah,kurang,set'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $keranjang = session('keranjang_web', []);
        $produkId = (string) $produk->id;

        if (! isset($keranjang[$produkId])) {
            return $this->responKeranjang($request, false, 'Produk tidak ditemukan di keranjang.');
        }

        $jumlahSekarang = (int) $keranjang[$produkId]['jumlah'];
        $aksi = $request->input('aksi');

        if ($aksi === 'tambah') {
            $jumlahSekarang++;
        }

        if ($aksi === 'kurang') {
            $jumlahSekarang--;
        }

        if ($aksi === 'set') {
            $jumlahSekarang = (int) $request->input('jumlah', 1);
        }

        $jumlahSekarang = max(1, $jumlahSekarang);
        $jumlahSekarang = min($jumlahSekarang, max(1, (int) $produk->stok));

        $keranjang[$produkId]['jumlah'] = $jumlahSekarang;

        session(['keranjang_web' => $keranjang]);

        return $this->responKeranjang($request, true, 'Jumlah produk berhasil diperbarui.');
    }

    public function destroy(Request $request, Produk $produk): JsonResponse|RedirectResponse
    {
        $keranjang = session('keranjang_web', []);
        $produkId = (string) $produk->id;

        unset($keranjang[$produkId]);

        session(['keranjang_web' => $keranjang]);

        return $this->responKeranjang($request, true, 'Produk berhasil dihapus dari keranjang.');
    }

    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        session()->forget('keranjang_web');

        return $this->responKeranjang($request, true, 'Keranjang berhasil dikosongkan.');
    }

    private function ambilDataKeranjang(): array
    {
        $keranjang = session('keranjang_web', []);

        $items = collect($keranjang)->map(function ($item) {
            $produk = Produk::query()
                ->with('gambarUtama')
                ->find($item['produk_id']);

            if (! $produk || ! $produk->aktif) {
                return null;
            }

            $jumlah = max(1, (int) $item['jumlah']);
            $harga = (float) $produk->harga;
            $subtotal = $jumlah * $harga;

            return [
                'produk' => $produk,
                'jumlah' => $jumlah,
                'harga' => $harga,
                'subtotal' => $subtotal,
                'stok_tersedia' => (int) $produk->stok,
            ];
        })->filter()->values();

        return [
            'items' => $items,
            'totalItem' => $items->sum('jumlah'),
            'totalBelanja' => $items->sum('subtotal'),
        ];
    }

    private function responKeranjang(Request $request, bool $success, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            $dataKeranjang = $this->ambilDataKeranjang();

            return response()->json([
                'success' => $success,
                'message' => $message,
                'total_item' => $dataKeranjang['totalItem'],
                'total_belanja' => $dataKeranjang['totalBelanja'],
                'total_belanja_format' => 'Rp ' . number_format($dataKeranjang['totalBelanja'], 0, ',', '.'),
                'items' => $dataKeranjang['items']->map(function ($item) {
                    return [
                        'produk_id' => $item['produk']->id,
                        'jumlah' => $item['jumlah'],
                        'subtotal' => $item['subtotal'],
                        'subtotal_format' => 'Rp ' . number_format($item['subtotal'], 0, ',', '.'),
                    ];
                })->values(),
            ]);
        }

        if ($success) {
            return back()->with('success', $message);
        }

        return back()->with('error', $message);
    }
}
