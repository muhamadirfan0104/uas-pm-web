<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
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
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('tanggal_pesanan')
            ->paginate(8)
            ->withQueryString();

        $jumlahStatus = [
            'semua' => Pesanan::query()
                ->where('user_id', $user->id)
                ->count(),

            'menunggu_pembayaran' => Pesanan::query()
                ->where('user_id', $user->id)
                ->where('status', 'menunggu_pembayaran')
                ->count(),

            'diproses' => Pesanan::query()
                ->where('user_id', $user->id)
                ->whereIn('status', ['dibayar', 'diproses', 'siap_diambil', 'dalam_pengantaran'])
                ->count(),

            'selesai' => Pesanan::query()
                ->where('user_id', $user->id)
                ->where('status', 'selesai')
                ->count(),

            'dibatalkan' => Pesanan::query()
                ->where('user_id', $user->id)
                ->where('status', 'dibatalkan')
                ->count(),
        ];

        return view('pembeli.pesanan', [
            'pesananList' => $pesananList,
            'status' => $status,
            'jumlahStatus' => $jumlahStatus,
        ]);
    }

    public function show(string $nomor_invoice): View
    {
        $pesanan = $this->ambilPesananLogin($nomor_invoice);

        return view('pembeli.detail-pesanan', compact('pesanan'));
    }

    public function cancel(string $nomor_invoice): RedirectResponse
    {
        $pesanan = $this->ambilPesananLogin($nomor_invoice);

        if ($pesanan->status !== 'menunggu_pembayaran') {
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