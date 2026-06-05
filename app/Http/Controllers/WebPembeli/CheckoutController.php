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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $dataKeranjang = $this->ambilDataKeranjang();

        if ($dataKeranjang['items']->isEmpty()) {
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

        return view('pembeli.checkout', [
            'items' => $dataKeranjang['items'],
            'totalItem' => $dataKeranjang['totalItem'],
            'subtotal' => $dataKeranjang['totalBelanja'],
            'pengaturan' => $pengaturan,
            'user' => $user,
            'alamatPembeli' => $alamatPembeli,
            'alamatUtama' => $alamatUtama,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dataKeranjang = $this->ambilDataKeranjang();

        if ($dataKeranjang['items']->isEmpty()) {
            return redirect()
                ->route('pembeli-web.keranjang.index')
                ->with('error', 'Keranjang masih kosong. Pilih produk dulu ya.');
        }

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
            'telepon' => ['required', 'string', 'max:30'],

            'metode_pengambilan' => ['required', Rule::in(['ambil_toko', 'kurir_toko'])],
            'metode_pembayaran' => ['required', Rule::in(['qris', 'tunai'])],

            'alamat_id' => ['nullable', 'integer', 'exists:alamat,id'],

            'catatan' => ['nullable', 'string', 'max:500'],
            'setuju' => ['accepted'],
        ], [
            'setuju.accepted' => 'Kamu perlu menyetujui pesanan sebelum checkout.',
            'alamat_id.exists' => 'Alamat yang dipilih tidak valid.',
        ]);

        if ($data['metode_pengambilan'] === 'kurir_toko') {
            $request->validate([
                'alamat_id' => ['required', 'integer', 'exists:alamat,id'],
            ], [
                'alamat_id.required' => 'Pilih alamat pengiriman terlebih dahulu untuk kurir toko.',
                'alamat_id.exists' => 'Alamat yang dipilih tidak valid.',
            ]);
        }

        try {
            $pesanan = DB::transaction(function () use ($data, $dataKeranjang) {
                $pengaturan = PengaturanToko::utama();
                $user = Auth::user();

                if (! $user || $user->role !== 'pembeli') {
                    throw new \RuntimeException('Silakan login sebagai pembeli dulu.');
                }

                $user->update([
                    'name' => $data['nama'],
                    'email' => $data['email'],
                    'telepon' => $data['telepon'],
                    'role' => $user->role ?: 'pembeli',
                    'aktif' => $user->aktif ?? true,
                ]);

                $alamat = null;

                if ($data['metode_pengambilan'] === 'kurir_toko') {
                    $alamat = Alamat::query()
                        ->where('user_id', $user->id)
                        ->where('id', $data['alamat_id'])
                        ->first();

                    if (! $alamat) {
                        throw new \RuntimeException('Alamat pengiriman tidak ditemukan.');
                    }
                }

                $items = [];
                $subtotal = 0;

                foreach ($dataKeranjang['items'] as $item) {
                    $produkKeranjang = $item['produk'];

                    $produk = Produk::query()
                        ->where('aktif', true)
                        ->lockForUpdate()
                        ->findOrFail($produkKeranjang->id);

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

                $biayaPengiriman = $data['metode_pengambilan'] === 'kurir_toko'
                    ? (float) ($pengaturan->biaya_minimum_pengiriman ?? 0)
                    : 0;

                $totalBayar = $subtotal + $biayaPengiriman;

                $pesanan = Pesanan::create([
                    'user_id' => $user->id,
                    'nomor_invoice' => 'INV-' . now()->format('YmdHis') . '-' . $user->id,
                    'tanggal_pesanan' => Carbon::now(),
                    'subtotal_produk' => $subtotal,
                    'jarak_km' => null,
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

                $referensiPembayaran = $metodePembayaran === 'tunai'
                    ? 'TUNAI-' . now()->format('YmdHis') . '-' . $pesanan->id
                    : 'QRIS-' . now()->format('YmdHis') . '-' . $pesanan->id;

                Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'metode_pembayaran' => $metodePembayaran,
                    'referensi_pembayaran' => $referensiPembayaran,
                    'jumlah' => $totalBayar,
                    'status' => 'menunggu_pembayaran',
                    'tautan_pembayaran' => $metodePembayaran === 'qris'
                        ? url('/pembeli-web/checkout/sukses/' . $pesanan->id)
                        : null,
                    'qr_code' => $metodePembayaran === 'qris'
                        ? 'QR-' . $pesanan->nomor_invoice
                        : null,
                ]);

                Pengiriman::create([
                    'pesanan_id' => $pesanan->id,
                    'metode' => $data['metode_pengambilan'],
                    'status_pengiriman' => null,
                    'alamat_toko' => $pengaturan->alamat,
                    'alamat_tujuan' => $alamat?->alamat_lengkap,
                    'latitude_tujuan' => $alamat?->latitude,
                    'longitude_tujuan' => $alamat?->longitude,
                    'jarak_km' => null,
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

            session()->forget('keranjang_web');

            return redirect()
                ->route('pembeli-web.checkout.success', $pesanan)
                ->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Checkout gagal. Coba ulangi lagi ya.');
        }
    }

    public function success(Pesanan $pesanan): View
    {
        $pesanan->load([
            'item.produk.gambarUtama',
            'pembayaran',
            'pengiriman',
            'alamatPengiriman',
            'user',
        ]);

        return view('pembeli.checkout-sukses', compact('pesanan'));
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
}