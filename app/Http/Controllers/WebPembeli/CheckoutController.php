<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Pembayaran;
use App\Models\PengaturanToko;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Services\WebPembeli\KeranjangService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private string $buyNowSessionKey = 'checkout_beli_sekarang';
    private string $selectedCartSessionKey = 'keranjang_web_selected';

    public function __construct(private KeranjangService $keranjangService)
    {
    }

    public function buyNow(Request $request, Produk $produk): RedirectResponse
    {
        if (! $produk->aktif) {
            return back()->with('error', 'Produk belum tersedia untuk dipesan.');
        }

        if ((int) $produk->stok <= 0) {
            return back()->with('error', 'Stok produk sedang habis.');
        }

        $request->validate([
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $jumlah = max(1, (int) $request->input('jumlah', 1));
        $jumlah = min($jumlah, max(1, (int) $produk->stok));

        // Alur seperti marketplace: Beli Sekarang tetap masuk ke keranjang,
        // produk yang dipilih langsung dicentang, lalu pembeli melanjutkan checkout dari keranjang.
        $this->keranjangService->tambah($produk, $jumlah);
        $this->keranjangService->updateJumlah($produk, 'set', $jumlah);
        $request->session()->forget($this->buyNowSessionKey);
        $request->session()->put($this->selectedCartSessionKey, [(int) $produk->id]);
        $request->session()->put('url.intended', route('pembeli-web.checkout.index'));

        return redirect()
            ->route('pembeli-web.keranjang.index')
            ->with('success', $produk->nama . ' sudah dipilih. Lanjutkan checkout dari keranjang.');
    }

    public function index(Request $request): View|RedirectResponse
    {
        $dataCheckout = $this->dataCheckout($request);

        if ($dataCheckout['items']->isEmpty()) {
            if ($dataCheckout['checkoutMode'] === 'buy_now') {
                $request->session()->forget($this->buyNowSessionKey);

                return redirect()
                    ->route('pembeli-web.produk')
                    ->with('error', 'Produk yang dipilih sudah tidak tersedia. Pilih produk lain terlebih dahulu.');
            }

            return redirect()
                ->route('pembeli-web.keranjang.index')
                ->with('error', 'Keranjang masih kosong. Pilih produk dulu ya.');
        }

        $pengaturan = PengaturanToko::utama();
        $user = Auth::user();

        $alamatPembeli = Alamat::query()
            ->where('user_id', $user->id)
            ->orderByDesc('utama')
            ->latest()
            ->get();

        $alamatUtama = $alamatPembeli->firstWhere('utama', true) ?? $alamatPembeli->first();
        $deliveryQuote = $this->hitungOngkirKurir($pengaturan, $alamatUtama, false);

        return view('pembeli.checkout', [
            'items' => $dataCheckout['items'],
            'totalItem' => $dataCheckout['totalItem'],
            'subtotal' => $dataCheckout['totalBelanja'],
            'checkoutMode' => $dataCheckout['checkoutMode'],
            'buyNowProduct' => $dataCheckout['buyNowProduct'],
            'pengaturan' => $pengaturan,
            'user' => $user,
            'alamatPembeli' => $alamatPembeli,
            'alamatUtama' => $alamatUtama,
            'deliveryQuote' => $deliveryQuote,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dataCheckout = $this->dataCheckout($request);

        if ($dataCheckout['items']->isEmpty()) {
            return redirect()
                ->route($dataCheckout['checkoutMode'] === 'buy_now' ? 'pembeli-web.produk' : 'pembeli-web.keranjang.index')
                ->with('error', 'Tidak ada produk yang bisa di-checkout. Pilih produk terlebih dahulu.');
        }

        $data = $request->validate([
            'alamat_id' => ['required', 'integer', 'exists:alamat,id'],
            'metode_pengambilan' => ['required', Rule::in(['ambil_toko', 'kurir_toko'])],
            'metode_pembayaran' => ['required', Rule::in(['cod', 'transfer_bank'])],
            'catatan' => ['nullable', 'string', 'max:500'],
            'setuju' => ['accepted'],
        ], [
            'alamat_id.required' => 'Pilih alamat penerima terlebih dahulu.',
            'alamat_id.exists' => 'Alamat yang dipilih tidak valid.',
            'setuju.accepted' => 'Kamu perlu menyetujui pesanan sebelum checkout.',
        ]);

        try {
            $pesanan = DB::transaction(function () use ($data, $dataCheckout, $request) {
                $pengaturan = PengaturanToko::utama();
                $user = Auth::user();

                if (! $user || $user->role !== 'pembeli') {
                    throw new \RuntimeException('Silakan login sebagai pembeli dulu.');
                }

                $user->update([
                    'role' => $user->role ?: 'pembeli',
                    'aktif' => $user->aktif ?? true,
                ]);

                $alamat = Alamat::query()
                    ->where('user_id', $user->id)
                    ->where('id', $data['alamat_id'])
                    ->first();

                if (! $alamat) {
                    throw new \RuntimeException('Alamat penerima tidak ditemukan.');
                }

                $items = [];
                $subtotal = 0;

                foreach ($dataCheckout['items'] as $item) {
                    $produkCheckout = $item['produk'];

                    $produk = Produk::query()
                        ->where('aktif', true)
                        ->lockForUpdate()
                        ->findOrFail($produkCheckout->id);

                    $jumlah = (int) $item['jumlah'];

                    if ($produk->stok < $jumlah) {
                        throw new \RuntimeException('Stok ' . $produk->nama . ' tidak mencukupi.');
                    }

                    $harga = (float) $produk->harga;
                    $rowSubtotal = $harga * $jumlah;

                    $subtotal += $rowSubtotal;

                    $items[] = [
                        'produk' => $produk,
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'subtotal' => $rowSubtotal,
                    ];
                }

                $deliveryQuote = $data['metode_pengambilan'] === 'kurir_toko'
                    ? $this->hitungOngkirKurir($pengaturan, $alamat, true)
                    : ['jarak' => null, 'biaya' => 0];

                $biayaPengiriman = (float) ($deliveryQuote['biaya'] ?? 0);
                $jarakKm = $deliveryQuote['jarak'] ?? null;
                $totalBayar = $subtotal + $biayaPengiriman;

                $pesanan = Pesanan::create([
                    'user_id' => $user->id,
                    'nomor_invoice' => 'INV-' . now()->format('YmdHis') . '-' . $user->id,
                    'tanggal_pesanan' => Carbon::now(),
                    'subtotal_produk' => $subtotal,
                    'jarak_km' => $jarakKm,
                    'biaya_pengiriman' => $biayaPengiriman,
                    'total_bayar' => $totalBayar,
                    'metode_pengambilan' => $data['metode_pengambilan'],
                    'alamat_pengiriman_id' => $alamat?->id,
                    'status' => 'menunggu_pembayaran',
                    'status_pembayaran' => 'menunggu_pembayaran',
                ]);

                foreach ($items as $item) {
                    $pesanan->item()->create([
                        'produk_id' => $item['produk']->id,
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $item['harga'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $item['produk']->decrement('stok', $item['jumlah']);

                    RiwayatStok::create([
                        'produk_id' => $item['produk']->id,
                        'perubahan' => -$item['jumlah'],
                        'tipe' => 'kurang',
                        'catatan' => 'Stok berkurang karena checkout web pembeli invoice ' . $pesanan->nomor_invoice,
                    ]);
                }

                $metodePembayaran = $data['metode_pembayaran'];

                if ($metodePembayaran === 'cod') {
                    $pesanan->update([
                        'status' => 'menunggu_konfirmasi',
                        'status_pembayaran' => 'menunggu_pembayaran',
                    ]);
                }

                $referensiPembayaran = $metodePembayaran === 'cod'
                    ? 'COD-' . now()->format('YmdHis') . '-' . $pesanan->id
                    : 'TRF-' . now()->format('YmdHis') . '-' . $pesanan->id;

                Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'metode_pembayaran' => $metodePembayaran,
                    'referensi_pembayaran' => $referensiPembayaran,
                    'jumlah' => $totalBayar,
                    'status' => 'menunggu_pembayaran',
                    'tautan_pembayaran' => null,
                    'qr_code' => null,
                    'bukti_transfer' => null,
                    'catatan_admin' => null,
                ]);

                Pengiriman::create([
                    'pesanan_id' => $pesanan->id,
                    'metode' => $data['metode_pengambilan'],
                    'status_pengiriman' => null,
                    'alamat_toko' => $pengaturan->alamat,
                    'alamat_tujuan' => $alamat?->alamat_lengkap,
                    'latitude_tujuan' => $alamat?->latitude,
                    'longitude_tujuan' => $alamat?->longitude,
                    'jarak_km' => $jarakKm,
                    'biaya' => $biayaPengiriman,
                ]);

                return $pesanan->load([
                    'item.produk',
                    'pembayaran',
                    'pengiriman',
                    'alamatPengiriman',
                    'user',
                ]);
            });

            if ($dataCheckout['checkoutMode'] === 'buy_now') {
                $request->session()->forget($this->buyNowSessionKey);
            } else {
                $this->keranjangService->hapusProdukIds($dataCheckout['selectedProductIds'] ?? []);
                $request->session()->forget($this->selectedCartSessionKey);
            }

            $redirect = redirect()
                ->route('pembeli-web.checkout.success', $pesanan)
                ->with('success', 'Pesanan berhasil dibuat.');

            if ($pesanan->pembayaran?->metode_pembayaran === 'transfer_bank') {
                $redirect->with('show_transfer_modal', true);
            }

            return $redirect;
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Checkout gagal. Coba ulangi lagi ya.');
        }
    }

    public function success(Pesanan $pesanan): View
    {
        abort_unless($pesanan->user_id === Auth::id(), 403);

        $pesanan->load([
            'item.produk.gambarUtama',
            'pembayaran',
            'pengiriman',
            'alamatPengiriman',
            'user',
        ]);

        $pengaturan = PengaturanToko::utama();

        return view('pembeli.checkout-sukses', compact('pesanan', 'pengaturan'));
    }

    private function dataCheckout(Request $request): array
    {
        $buyNow = $request->session()->get($this->buyNowSessionKey);

        if ($buyNow) {
            $produk = Produk::query()
                ->with('gambarUtama')
                ->where('aktif', true)
                ->find($buyNow['produk_id'] ?? null);

            if (! $produk || (int) $produk->stok <= 0) {
                return [
                    'items' => collect(),
                    'totalItem' => 0,
                    'totalBelanja' => 0,
                    'checkoutMode' => 'buy_now',
                    'buyNowProduct' => null,
                    'selectedProductIds' => [],
                ];
            }

            $jumlah = max(1, (int) ($buyNow['jumlah'] ?? 1));
            $jumlah = min($jumlah, max(1, (int) $produk->stok));
            $harga = (float) $produk->harga;
            $subtotal = $harga * $jumlah;

            $items = collect([
                [
                    'produk' => $produk,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'stok_tersedia' => (int) $produk->stok,
                ],
            ]);

            return [
                'items' => $items,
                'totalItem' => $jumlah,
                'totalBelanja' => $subtotal,
                'checkoutMode' => 'buy_now',
                'buyNowProduct' => $produk,
                'selectedProductIds' => [(int) $produk->id],
            ];
        }

        $dataKeranjang = $this->keranjangService->data();

        $selectedProductIds = collect($request->session()->get($this->selectedCartSessionKey, []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($selectedProductIds->isEmpty()) {
            return [
                'items' => collect(),
                'totalItem' => 0,
                'totalBelanja' => 0,
                'checkoutMode' => 'cart',
                'buyNowProduct' => null,
                'selectedProductIds' => [],
            ];
        }

        $items = $dataKeranjang['items']
            ->filter(fn ($item) => $selectedProductIds->contains((int) $item['produk']->id))
            ->values();

        return [
            'items' => $items,
            'totalItem' => (int) $items->sum('jumlah'),
            'totalBelanja' => (float) $items->sum('subtotal'),
            'checkoutMode' => 'cart',
            'buyNowProduct' => null,
            'selectedProductIds' => $selectedProductIds->all(),
        ];
    }

    private function hitungOngkirKurir(PengaturanToko $pengaturan, ?Alamat $alamat, bool $strict = true): array
    {
        $tarifPerKm = max(0, (float) ($pengaturan->tarif_per_km ?? 0));
        $minimum = max(0, (float) ($pengaturan->biaya_minimum_pengiriman ?? 0));
        $radius = (float) ($pengaturan->radius_maksimal_km ?? 0);

        if (! $alamat) {
            if ($strict) {
                throw new \RuntimeException('Pilih alamat penerima terlebih dahulu.');
            }
            return ['jarak' => null, 'biaya' => $minimum];
        }

        $storeLat = $pengaturan->latitude_toko;
        $storeLng = $pengaturan->longitude_toko;
        $destLat = $alamat->latitude;
        $destLng = $alamat->longitude;

        if ($storeLat === null || $storeLng === null || $destLat === null || $destLng === null) {
            if ($strict) {
                throw new \RuntimeException('Titik lokasi toko atau alamat penerima belum lengkap. Pilih titik lokasi di maps terlebih dahulu.');
            }
            return ['jarak' => null, 'biaya' => $minimum];
        }

        $jarak = $this->haversineKm((float) $storeLat, (float) $storeLng, (float) $destLat, (float) $destLng);

        if ($radius > 0 && $jarak > $radius) {
            if ($strict) {
                throw new \RuntimeException('Alamat berada di luar radius pengiriman toko. Jarak alamat sekitar ' . number_format($jarak, 2, ',', '.') . ' km.');
            }
        }

        $biaya = max($minimum, ceil($jarak * $tarifPerKm / 100) * 100);

        return [
            'jarak' => round($jarak, 2),
            'biaya' => $biaya,
        ];
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

}
