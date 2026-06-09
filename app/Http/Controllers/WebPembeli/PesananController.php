<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\PengaturanToko;
use App\Models\RiwayatStok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $status = trim((string) $request->query('status'));
        $q = trim((string) $request->query('q'));

        $statusGroups = [
            'menunggu_pembayaran' => ['menunggu_pembayaran', 'menunggu_verifikasi'],
            'diproses' => ['menunggu_konfirmasi', 'diproses', 'disiapkan'],
            'siap_diterima' => ['siap_diambil', 'dalam_pengantaran'],
            'selesai' => ['selesai'],
            'dibatalkan' => ['dibatalkan'],
        ];

        $pesananList = Pesanan::query()
            ->with([
                'user',
                'item.produk.gambarUtama',
                'pembayaran',
                'pengiriman',
                'alamatPengiriman',
                'ulasan',
            ])
            ->where('user_id', $user->id)
            ->when($status !== '' && isset($statusGroups[$status]), function ($query) use ($statusGroups, $status) {
                $query->whereIn('status', $statusGroups[$status]);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery
                        ->where('nomor_invoice', 'like', '%' . $q . '%')
                        ->orWhereHas('item.produk', function ($productQuery) use ($q) {
                            $productQuery->where('nama', 'like', '%' . $q . '%');
                        });
                });
            })
            ->latest('tanggal_pesanan')
            ->paginate(6)
            ->withQueryString();

        $baseCountQuery = Pesanan::query()->where('user_id', $user->id);

        $jumlahStatus = [
            'semua' => (clone $baseCountQuery)->count(),
            'menunggu_pembayaran' => (clone $baseCountQuery)->whereIn('status', $statusGroups['menunggu_pembayaran'])->count(),
            'diproses' => (clone $baseCountQuery)->whereIn('status', $statusGroups['diproses'])->count(),
            'siap_diterima' => (clone $baseCountQuery)->whereIn('status', $statusGroups['siap_diterima'])->count(),
            'selesai' => (clone $baseCountQuery)->whereIn('status', $statusGroups['selesai'])->count(),
            'dibatalkan' => (clone $baseCountQuery)->whereIn('status', $statusGroups['dibatalkan'])->count(),
        ];

        return view('pembeli.pesanan', [
            'pesananList' => $pesananList,
            'status' => $status,
            'q' => $q,
            'jumlahStatus' => $jumlahStatus,
        ]);
    }

    public function show(string $nomor_invoice): View
    {
        $pesanan = $this->ambilPesananLogin($nomor_invoice);
        $pengaturan = PengaturanToko::utama();

        return view('pembeli.detail-pesanan', compact('pesanan', 'pengaturan'));
    }

    public function cancel(string $nomor_invoice): RedirectResponse
    {
        $pesanan = $this->ambilPesananLogin($nomor_invoice);

        if (! in_array($pesanan->status, ['menunggu_pembayaran', 'menunggu_verifikasi', 'menunggu_konfirmasi'], true)) {
            return back()->with('error', 'Pesanan tidak bisa dibatalkan karena sudah masuk proses toko.');
        }

        if ($pesanan->status_pembayaran === 'dibayar') {
            return back()->with('error', 'Pesanan yang sudah dibayar tidak bisa dibatalkan dari halaman pembeli.');
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->loadMissing(['item.produk', 'pembayaran', 'pengiriman']);

            foreach ($pesanan->item as $item) {
                if (! $item->produk) {
                    continue;
                }

                $item->produk->increment('stok', (int) $item->jumlah);

                RiwayatStok::create([
                    'produk_id' => $item->produk_id,
                    'perubahan' => (int) $item->jumlah,
                    'tipe' => 'tambah',
                    'catatan' => 'Stok dikembalikan karena pesanan dibatalkan pembeli invoice ' . $pesanan->nomor_invoice,
                ]);
            }

            $pesanan->update([
                'status' => 'dibatalkan',
                'status_pembayaran' => 'dibatalkan',
            ]);

            $pesanan->pembayaran?->update([
                'status' => 'dibatalkan',
                'dibayar_pada' => null,
            ]);

            $pesanan->pengiriman?->update([
                'status_pengiriman' => null,
            ]);
        });

        return redirect()
            ->route('pembeli-web.pesanan.show', $pesanan->nomor_invoice)
            ->with('success', 'Pesanan berhasil dibatalkan dan stok produk sudah dikembalikan.');
    }

    public function uploadBuktiPembayaran(Request $request, string $nomor_invoice): RedirectResponse
    {
        $pesanan = $this->ambilPesananLogin($nomor_invoice);
        $pembayaran = $pesanan->pembayaran;

        if (! $pembayaran || $pembayaran->metode_pembayaran !== 'transfer_bank') {
            return back()->with('error', 'Upload bukti hanya tersedia untuk metode transfer bank.');
        }

        if (! in_array($pembayaran->status, ['menunggu_pembayaran', 'menunggu_verifikasi', 'ditolak'], true)) {
            return back()->with('error', 'Bukti pembayaran tidak bisa diubah karena status pembayaran sudah diproses.');
        }

        $data = $request->validate([
            'bukti_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ], [
            'bukti_transfer.required' => 'Pilih file bukti transfer terlebih dahulu.',
            'bukti_transfer.mimes' => 'Bukti transfer harus berupa JPG, PNG, WEBP, atau PDF.',
            'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 4 MB.',
        ]);

        $path = $data['bukti_transfer']->store('bukti-transfer', 'public');

        DB::transaction(function () use ($pesanan, $pembayaran, $path) {
            $pembayaran->update([
                'bukti_transfer' => $path,
                'status' => 'menunggu_verifikasi',
                'catatan_admin' => null,
                'dibayar_pada' => null,
                'diverifikasi_pada' => null,
            ]);

            $pesanan->update([
                'status' => 'menunggu_verifikasi',
                'status_pembayaran' => 'menunggu_verifikasi',
            ]);
        });

        return back()->with('success', 'Bukti transfer berhasil diupload. Toko akan memeriksa pembayaran Anda.');
    }

    public function confirmReceived(string $nomor_invoice): RedirectResponse
    {
        $pesanan = $this->ambilPesananLogin($nomor_invoice);

        if (! in_array($pesanan->status, ['siap_diambil', 'dalam_pengantaran'], true)) {
            return back()->with('error', 'Pesanan belum bisa dikonfirmasi diterima.');
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->update([
                'status' => 'selesai',
            ]);

            $pesanan->pengiriman?->update([
                'status_pengiriman' => 'selesai',
            ]);
        });

        return redirect()
            ->route('pembeli-web.pesanan.show', $pesanan->nomor_invoice)
            ->with('success', 'Pesanan berhasil dikonfirmasi diterima.');
    }

    private function ambilPesananLogin(string $nomor_invoice): Pesanan
    {
        $user = Auth::user();

        return Pesanan::query()
            ->with([
                'user',
                'item.produk.gambarUtama',
                'pembayaran',
                'pengiriman',
                'alamatPengiriman',
                'ulasan',
            ])
            ->where('user_id', $user->id)
            ->where('nomor_invoice', $nomor_invoice)
            ->firstOrFail();
    }
}