<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\View\View;

class QrPesananController extends Controller
{
    public function show(string $nomor_invoice): View
    {
        $pesanan = Pesanan::query()
            ->with([
                'user',
                'item.produk',
                'pembayaran',
                'pengiriman',
            ])
            ->where('nomor_invoice', $nomor_invoice)
            ->first();

        $valid = false;
        $pesanValidasi = 'Pesanan tidak ditemukan.';

        if ($pesanan) {
            $pembayaran = $pesanan->pembayaran;

            $sudahDibayar = $pesanan->status_pembayaran === 'dibayar'
                && $pembayaran
                && $pembayaran->status === 'dibayar';

            $siapDiambil = $pesanan->metode_pengambilan === 'ambil_toko'
                && $pesanan->status === 'siap_diambil';

            $belumSelesai = $pesanan->status !== 'selesai'
                && $pesanan->status !== 'dibatalkan';

            $valid = $sudahDibayar && $siapDiambil && $belumSelesai;

            if ($valid) {
                $pesanValidasi = 'Pesanan valid. Pembayaran sudah lunas dan pesanan siap diambil.';
            } elseif (! $sudahDibayar) {
                $pesanValidasi = 'Pesanan belum boleh diserahkan karena pembayaran belum lunas / belum diverifikasi.';
            } elseif (! $siapDiambil) {
                $pesanValidasi = 'Pesanan belum berada pada status siap diambil.';
            } elseif (! $belumSelesai) {
                $pesanValidasi = 'Pesanan sudah selesai atau dibatalkan.';
            }
        }

        return view('qr.pesanan', [
            'pesanan' => $pesanan,
            'valid' => $valid,
            'pesanValidasi' => $pesanValidasi,
        ]);
    }
}