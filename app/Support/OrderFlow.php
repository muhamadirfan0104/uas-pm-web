<?php

namespace App\Support;

use App\Models\Pengiriman;
use App\Models\Pesanan;
use RuntimeException;

class OrderFlow
{
    public static function paymentMethod(?Pesanan $order): ?string
    {
        return $order?->pembayaran?->metode_pembayaran;
    }

    public static function isCod(?Pesanan $order): bool
    {
        return self::paymentMethod($order) === 'cod';
    }

    public static function isPaid(?Pesanan $order): bool
    {
        return $order?->status_pembayaran === 'dibayar' || $order?->pembayaran?->status === 'dibayar';
    }

    public static function canProcess(?Pesanan $order): bool
    {
        return $order && (self::isCod($order) || self::isPaid($order));
    }

    public static function nextOrderStatus(?Pesanan $order): ?string
    {
        if (! $order || in_array($order->status, ['selesai', 'dibatalkan'], true)) {
            return null;
        }

        return match ($order->status) {
            'menunggu_pembayaran' => self::canProcess($order) ? 'diproses' : null,
            'dibayar' => 'diproses',
            'diproses' => $order->metode_pengambilan === 'kurir_toko' ? 'dalam_pengantaran' : 'siap_diambil',
            'siap_diambil', 'dalam_pengantaran' => 'selesai',
            default => null,
        };
    }

    public static function nextShippingStatus(?Pengiriman $shipment): ?string
    {
        if (! $shipment || $shipment->status_pengiriman === 'selesai') {
            return null;
        }

        if (! $shipment->status_pengiriman) {
            return $shipment->metode === 'kurir_toko' ? 'dalam_pengantaran' : 'siap_diambil';
        }

        return match ($shipment->status_pengiriman) {
            'siap_diambil', 'dalam_pengantaran' => 'selesai',
            default => null,
        };
    }

    public static function assertOrderTransition(Pesanan $order, string $targetStatus): void
    {
        if ($targetStatus === $order->status) {
            return;
        }

        if ($targetStatus === 'dibatalkan') {
            if (in_array($order->status, ['selesai', 'dibatalkan'], true)) {
                throw new RuntimeException('Pesanan yang sudah selesai atau dibatalkan tidak bisa dibatalkan lagi.');
            }
            return;
        }

        $next = self::nextOrderStatus($order);

        if ($targetStatus !== $next) {
            throw new RuntimeException('Status pesanan harus mengikuti alur: belum bayar, diproses, lalu ambil/kirim, dan selesai.');
        }

        if (in_array($targetStatus, ['diproses', 'siap_diambil', 'dalam_pengantaran', 'selesai'], true) && ! self::canProcess($order)) {
            throw new RuntimeException('Pesanan transfer bank belum bisa diproses sebelum pembayaran diterima. Pesanan COD boleh diproses lebih dulu dan dibayar saat selesai.');
        }

        if ($targetStatus === 'siap_diambil' && $order->metode_pengambilan !== 'ambil_toko') {
            throw new RuntimeException('Status siap diambil hanya untuk pesanan ambil di toko.');
        }

        if ($targetStatus === 'dalam_pengantaran' && $order->metode_pengambilan !== 'kurir_toko') {
            throw new RuntimeException('Status dalam pengantaran hanya untuk pesanan kurir toko.');
        }
    }

    public static function assertShippingTransition(Pengiriman $shipment, string $targetStatus): void
    {
        $shipment->loadMissing('pesanan.pembayaran');
        $order = $shipment->pesanan;

        if (! $order) {
            throw new RuntimeException('Pesanan tidak ditemukan.');
        }

        if (! self::canProcess($order)) {
            throw new RuntimeException('Pesanan transfer bank belum bisa masuk pengambilan/kirim sebelum pembayaran diterima. Pesanan COD boleh diproses lalu dibayar saat selesai.');
        }

        if (! $shipment->status_pengiriman && $order->status !== 'diproses') {
            throw new RuntimeException('Pesanan harus masuk tahap diproses terlebih dahulu sebelum pengambilan atau pengiriman disiapkan.');
        }

        if ($shipment->status_pengiriman && ! in_array($order->status, ['siap_diambil', 'dalam_pengantaran'], true)) {
            throw new RuntimeException('Pesanan belum berada pada tahap pengambilan atau pengiriman yang bisa diselesaikan.');
        }

        if ($targetStatus === 'siap_diambil' && $shipment->metode !== 'ambil_toko') {
            throw new RuntimeException('Status siap diambil hanya untuk pesanan ambil di toko.');
        }

        if ($targetStatus === 'dalam_pengantaran' && $shipment->metode !== 'kurir_toko') {
            throw new RuntimeException('Status dalam pengantaran hanya untuk pesanan kurir toko.');
        }

        $next = self::nextShippingStatus($shipment);
        if ($targetStatus !== $next) {
            throw new RuntimeException('Status pengambilan/kirim harus mengikuti alur yang benar.');
        }
    }

    public static function shippingStatusToOrderStatus(string $shippingStatus): string
    {
        return match ($shippingStatus) {
            'siap_diambil' => 'siap_diambil',
            'dalam_pengantaran' => 'dalam_pengantaran',
            'selesai' => 'selesai',
            default => 'diproses',
        };
    }

    public static function steps(?Pesanan $order): array
    {
        if ($order?->metode_pengambilan === 'kurir_toko') {
            return ['menunggu_pembayaran', 'diproses', 'dalam_pengantaran', 'selesai'];
        }

        return ['menunggu_pembayaran', 'diproses', 'siap_diambil', 'selesai'];
    }
}
