<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Services\WebPembeli\KeranjangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    private string $selectedSessionKey = 'keranjang_web_selected';

    public function __construct(private KeranjangService $keranjangService)
    {
    }

    public function index(): View
    {
        $dataKeranjang = $this->keranjangService->data();
        $productIds = $dataKeranjang['items']
            ->map(fn ($item) => (int) $item['produk']->id)
            ->values()
            ->all();

        $selectedProductIds = session($this->selectedSessionKey, $productIds);
        $selectedProductIds = collect($selectedProductIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $productIds, true))
            ->unique()
            ->values()
            ->all();

        if ($dataKeranjang['items']->count() && ! session()->has($this->selectedSessionKey)) {
            session([$this->selectedSessionKey => $selectedProductIds]);
        }

        return view('pembeli.keranjang', [
            'items' => $dataKeranjang['items'],
            'totalItem' => $dataKeranjang['totalItem'],
            'totalBelanja' => $dataKeranjang['totalBelanja'],
            'selectedProductIds' => $selectedProductIds,
        ]);
    }

    public function store(Request $request, Produk $produk): JsonResponse|RedirectResponse
    {
        if (! $produk->aktif) {
            return $this->responKeranjang($request, false, 'Produk belum tersedia.');
        }

        if ((int) $produk->stok <= 0) {
            return $this->responKeranjang($request, false, 'Stok produk sedang habis.');
        }

        $request->validate([
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $jumlah = max(1, (int) $request->input('jumlah', 1));
        $this->keranjangService->tambah($produk, $jumlah);

        $this->gabungkanPilihan([$produk->id]);

        return $this->responKeranjang($request, true, $produk->nama . ' berhasil masuk keranjang.');
    }

    public function update(Request $request, Produk $produk): JsonResponse|RedirectResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:tambah,kurang,set'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $berhasil = $this->keranjangService->updateJumlah(
            $produk,
            (string) $request->input('aksi'),
            $request->filled('jumlah') ? (int) $request->input('jumlah') : null
        );

        if (! $berhasil) {
            return $this->responKeranjang($request, false, 'Produk tidak ditemukan di keranjang.');
        }

        return $this->responKeranjang($request, true, 'Jumlah produk berhasil diperbarui.');
    }

    public function checkoutSelected(Request $request): RedirectResponse
    {
        $dataKeranjang = $this->keranjangService->data();
        $cartProductIds = $dataKeranjang['items']
            ->map(fn ($item) => (int) $item['produk']->id)
            ->values();

        $selectedProductIds = collect($request->input('selected_produk', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && $cartProductIds->contains($id))
            ->unique()
            ->values();

        if ($selectedProductIds->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu produk.');
        }

        session([$this->selectedSessionKey => $selectedProductIds->all()]);
        $request->session()->put('url.intended', route('pembeli-web.checkout.index'));

        if (! Auth::check() || Auth::user()?->role !== 'pembeli') {
            return back()
                ->with('auth_modal', 'login')
                ->with('success', 'Login diperlukan untuk melanjutkan checkout.');
        }

        return redirect()->route('pembeli-web.checkout.index');
    }

    public function destroy(Request $request, Produk $produk): JsonResponse|RedirectResponse
    {
        $this->keranjangService->hapus($produk);
        $this->hapusDariPilihan([$produk->id]);

        return $this->responKeranjang($request, true, 'Produk berhasil dihapus dari keranjang.');
    }

    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $this->keranjangService->kosongkan();
        $request->session()->forget($this->selectedSessionKey);

        return $this->responKeranjang($request, true, 'Keranjang berhasil dikosongkan.');
    }

    private function responKeranjang(Request $request, bool $success, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            $dataKeranjang = $this->keranjangService->data();

            return response()->json([
                'success' => $success,
                'message' => $message,
                'total_item' => $dataKeranjang['totalItem'],
                'total_belanja' => $dataKeranjang['totalBelanja'],
                'total_belanja_format' => 'Rp ' . number_format($dataKeranjang['totalBelanja'], 0, ',', '.'),
                'items' => $dataKeranjang['items']->map(function ($item) {
                    return [
                        'produk_id' => $item['produk']->id,
                        'nama' => $item['produk']->nama,
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'harga_format' => 'Rp ' . number_format($item['harga'], 0, ',', '.'),
                        'subtotal' => $item['subtotal'],
                        'subtotal_format' => 'Rp ' . number_format($item['subtotal'], 0, ',', '.'),
                    ];
                })->values(),
                'mini_cart_html' => view('pembeli.partials.mini-cart-dropdown', [
                    'miniCartItems' => $dataKeranjang['items']->take(5),
                    'miniCartTotalItem' => $dataKeranjang['totalItem'],
                    'miniCartTotalBelanja' => $dataKeranjang['totalBelanja'],
                ])->render(),
            ], $success ? 200 : 422);
        }

        if ($success) {
            return back()->with('success', $message);
        }

        return back()->with('error', $message);
    }

    private function gabungkanPilihan(array $produkIds): void
    {
        $pilihan = collect(session($this->selectedSessionKey, []))
            ->merge($produkIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        session([$this->selectedSessionKey => $pilihan]);
    }

    private function hapusDariPilihan(array $produkIds): void
    {
        $hapus = collect($produkIds)->map(fn ($id) => (int) $id)->all();

        $pilihan = collect(session($this->selectedSessionKey, []))
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => in_array($id, $hapus, true))
            ->values()
            ->all();

        session([$this->selectedSessionKey => $pilihan]);
    }
}
