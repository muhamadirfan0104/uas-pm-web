<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanToko;
use App\Models\Pengiriman;
use App\Support\OrderFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengirimanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pengiriman::with(['pesanan.user', 'pesanan.pembayaran', 'pesanan.alamatPengiriman', 'pesanan.item.produk.gambarUtama'])
            ->whereHas('pesanan', fn ($order) => $order->whereIn('status', ['disiapkan', 'siap_diambil', 'dalam_pengantaran']))
            ->latest();

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('alamat_tujuan', 'like', '%' . $keyword . '%')
                    ->orWhereHas('pesanan', function ($order) use ($keyword) {
                        $order->where('nomor_invoice', 'like', '%' . $keyword . '%')
                            ->orWhereHas('user', function ($user) use ($keyword) {
                                $user->where('name', 'like', '%' . $keyword . '%')
                                    ->orWhere('telepon', 'like', '%' . $keyword . '%')
                                    ->orWhere('email', 'like', '%' . $keyword . '%');
                            });
                    });
            });
        }

        $tab = (string) $request->input('tab', 'semua');
        if ($tab !== 'semua') {
            match ($tab) {
                'belum_diproses' => $query->whereNull('status_pengiriman')->whereHas('pesanan', fn ($order) => $order->where('status', 'disiapkan')),
                'siap_diambil' => $query->where('status_pengiriman', 'siap_diambil'),
                'dalam_pengantaran' => $query->where('status_pengiriman', 'dalam_pengantaran'),
                'ambil_toko' => $query->where('metode', 'ambil_toko'),
                'kurir_toko' => $query->where('metode', 'kurir_toko'),
                default => null,
            };
        }

        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        if ($request->filled('status')) {
            if ($request->status === 'belum_diproses') {
                $query->whereNull('status_pengiriman')->whereHas('pesanan', fn ($order) => $order->where('status', 'disiapkan'));
            } elseif (in_array($request->status, ['siap_diambil', 'dalam_pengantaran'], true)) {
                $query->where('status_pengiriman', $request->status);
            }
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $pengiriman = $query->paginate(10)->withQueryString();
        $pengaturan = PengaturanToko::utama();

        $fulfillmentBase = fn () => Pengiriman::query()->whereHas('pesanan', fn ($q) => $q->whereIn('status', ['disiapkan', 'siap_diambil', 'dalam_pengantaran']));
        $stats = [
            'belum_diproses' => $fulfillmentBase()->whereNull('status_pengiriman')->whereHas('pesanan', fn ($q) => $q->where('status', 'disiapkan'))->count(),
            'siap_diambil' => $fulfillmentBase()->where('status_pengiriman', 'siap_diambil')->count(),
            'dalam_pengantaran' => $fulfillmentBase()->where('status_pengiriman', 'dalam_pengantaran')->count(),
            'kurir_toko' => $fulfillmentBase()->where('metode', 'kurir_toko')->count(),
            'ambil_toko' => $fulfillmentBase()->where('metode', 'ambil_toko')->count(),
        ];

        $mapLat = old('latitude_toko', $pengaturan->latitude_toko ?: -7.2575);
        $mapLng = old('longitude_toko', $pengaturan->longitude_toko ?: 112.7521);

        return view('admin.pengiriman.index', compact('pengiriman', 'pengaturan', 'stats', 'mapLat', 'mapLng'));
    }

    public function updatePengaturan(Request $request): RedirectResponse
    {
        $pengaturan = PengaturanToko::utama();

        $data = $request->validate([
            'alamat' => ['nullable', 'string'],
            'jam_buka' => ['nullable', 'string', 'max:50'],
            'jam_tutup' => ['nullable', 'string', 'max:50'],
            'latitude_toko' => ['nullable', 'numeric'],
            'longitude_toko' => ['nullable', 'numeric'],
            'tarif_per_km' => ['nullable', 'numeric', 'min:0'],
            'biaya_minimum_pengiriman' => ['nullable', 'numeric', 'min:0'],
            'radius_maksimal_km' => ['nullable', 'numeric', 'min:0'],
            'area_pengiriman' => ['nullable', 'string', 'max:255'],
        ]);

        $pengaturan->update($data);

        return back()->with('success', 'Pengaturan pengambilan dan pengiriman berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Pengiriman $pengiriman): RedirectResponse
    {
        $data = $request->validate([
            'status_pengiriman' => ['required', 'in:siap_diambil,dalam_pengantaran,selesai'],
        ]);

        try {
            DB::transaction(function () use ($pengiriman, $data) {
                $pengiriman->loadMissing(['pesanan.pembayaran']);

                OrderFlow::assertShippingTransition($pengiriman, $data['status_pengiriman']);

                $statusPesanan = OrderFlow::shippingStatusToOrderStatus($data['status_pengiriman']);
                $order = $pengiriman->pesanan;
                $metodeBayar = $order?->pembayaran?->metode_pembayaran;

                $pengiriman->update($data);

                if ($order) {
                    $order->status = $statusPesanan;

                    if ($statusPesanan === 'selesai' && $metodeBayar === 'cod') {
                        $order->status_pembayaran = 'dibayar';
                        $order->pembayaran?->update([
                            'status' => 'dibayar',
                            'dibayar_pada' => now(),
                            'diverifikasi_pada' => now(),
                        ]);
                    }

                    $order->save();
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Status pengambilan/pengiriman gagal diperbarui.');
        }

        return back()->with('success', 'Status pengambilan/pengiriman berhasil diperbarui.');
    }
}
