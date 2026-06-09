<?php

namespace App\Support;

use App\Models\Pengiriman;
use App\Models\Pesanan;
use RuntimeException;

class OrderFlow
{
    public const ORDER_WAITING_PAYMENT = 'menunggu_pembayaran';
    public const ORDER_WAITING_VERIFICATION = 'menunggu_verifikasi';
    public const ORDER_WAITING_CONFIRMATION = 'menunggu_konfirmasi';
    public const ORDER_PROCESSING = 'diproses';
    public const ORDER_PREPARED = 'disiapkan';
    public const ORDER_READY_PICKUP = 'siap_diambil';
    public const ORDER_DELIVERING = 'dalam_pengantaran';
    public const ORDER_DONE = 'selesai';
    public const ORDER_CANCELLED = 'dibatalkan';

    public static function paymentMethod(?Pesanan $order): ?string
    {
        return $order?->pembayaran?->metode_pembayaran;
    }

    public static function isCod(?Pesanan $order): bool
    {
        return self::paymentMethod($order) === 'cod';
    }

    public static function isTransfer(?Pesanan $order): bool
    {
        return self::paymentMethod($order) === 'transfer_bank';
    }

    public static function isPaid(?Pesanan $order): bool
    {
        return $order?->status_pembayaran === 'dibayar'
            || $order?->pembayaran?->status === 'dibayar';
    }

    public static function canEnterOrderWork(?Pesanan $order): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Transfer bank:
        |--------------------------------------------------------------------------
        | Baru boleh diproses kalau pembayaran sudah dibayar.
        |
        | COD:
        |--------------------------------------------------------------------------
        | Boleh diproses walaupun pembayaran masih menunggu,
        | karena COD dibayar saat pesanan diterima / diambil.
        */
        return $order && (self::isCod($order) || self::isPaid($order));
    }

    public static function canCancel(?Pesanan $order): bool
    {
        return $order && ! in_array($order->status, [
            self::ORDER_DONE,
            self::ORDER_CANCELLED,
        ], true);
    }

    public static function nextOrderStatus(?Pesanan $order): ?string
    {
        if (! $order || in_array($order->status, [
            self::ORDER_DONE,
            self::ORDER_CANCELLED,
        ], true)) {
            return null;
        }

        return match ($order->status) {
            self::ORDER_WAITING_CONFIRMATION => self::canEnterOrderWork($order)
                ? self::ORDER_PROCESSING
                : null,

            self::ORDER_PROCESSING => self::ORDER_PREPARED,

            self::ORDER_PREPARED => $order->metode_pengambilan === 'kurir_toko'
                ? self::ORDER_DELIVERING
                : self::ORDER_READY_PICKUP,

            self::ORDER_READY_PICKUP,
            self::ORDER_DELIVERING => self::ORDER_DONE,

            default => null,
        };
    }

    public static function nextShippingStatus(?Pengiriman $shipment): ?string
    {
        if (! $shipment || $shipment->status_pengiriman === self::ORDER_DONE) {
            return null;
        }

        $shipment->loadMissing('pesanan.pembayaran');

        $order = $shipment->pesanan;

        if (! $shipment->status_pengiriman) {
            return $order?->status === self::ORDER_PREPARED
                ? ($shipment->metode === 'kurir_toko'
                    ? self::ORDER_DELIVERING
                    : self::ORDER_READY_PICKUP)
                : null;
        }

        return match ($shipment->status_pengiriman) {
            self::ORDER_READY_PICKUP,
            self::ORDER_DELIVERING => self::ORDER_DONE,

            default => null,
        };
    }

    public static function assertOrderTransition(Pesanan $order, string $targetStatus): void
    {
        $order->loadMissing(['pembayaran', 'pengiriman']);

        if ($targetStatus === $order->status) {
            return;
        }

        if ($targetStatus === self::ORDER_CANCELLED) {
            if (! self::canCancel($order)) {
                throw new RuntimeException('Pesanan yang sudah selesai atau dibatalkan tidak bisa dibatalkan lagi.');
            }

            return;
        }

        if ($targetStatus === self::ORDER_WAITING_CONFIRMATION && self::isPaid($order)) {
            return;
        }

        $next = self::nextOrderStatus($order);

        if ($targetStatus !== $next) {
            throw new RuntimeException('Status pesanan harus mengikuti alur: menunggu konfirmasi, diproses, disiapkan, siap diambil atau dalam pengantaran, lalu selesai.');
        }

        if (
            in_array($targetStatus, [
                self::ORDER_PROCESSING,
                self::ORDER_PREPARED,
                self::ORDER_READY_PICKUP,
                self::ORDER_DELIVERING,
                self::ORDER_DONE,
            ], true)
            && ! self::canEnterOrderWork($order)
        ) {
            throw new RuntimeException('Pesanan transfer bank belum bisa diproses sebelum pembayaran diterima. Pesanan COD boleh diproses dan dibayar saat pesanan selesai.');
        }
    }

    public static function assertShippingTransition(Pengiriman $shipment, string $targetStatus): void
    {
        $shipment->loadMissing('pesanan.pembayaran');

        $order = $shipment->pesanan;

        if (! $order) {
            throw new RuntimeException('Pesanan tidak ditemukan.');
        }

        if (! self::canEnterOrderWork($order)) {
            throw new RuntimeException('Pesanan transfer bank belum bisa masuk pengambilan/kirim sebelum pembayaran diterima. Pesanan COD boleh lanjut dan dibayar saat selesai.');
        }

        if (! $shipment->status_pengiriman && $order->status !== self::ORDER_PREPARED) {
            throw new RuntimeException('Pesanan harus disiapkan terlebih dahulu sebelum masuk tahap pengambilan atau pengantaran.');
        }

        if ($targetStatus === self::ORDER_READY_PICKUP && $shipment->metode !== 'ambil_toko') {
            throw new RuntimeException('Status siap diambil hanya untuk pesanan ambil di toko.');
        }

        if ($targetStatus === self::ORDER_DELIVERING && $shipment->metode !== 'kurir_toko') {
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
            self::ORDER_READY_PICKUP => self::ORDER_READY_PICKUP,
            self::ORDER_DELIVERING => self::ORDER_DELIVERING,
            self::ORDER_DONE => self::ORDER_DONE,
            default => self::ORDER_PREPARED,
        };
    }

    public static function steps(?Pesanan $order): array
    {
        $base = [
            self::ORDER_WAITING_PAYMENT,
        ];

        if (self::isTransfer($order)) {
            $base[] = self::ORDER_WAITING_VERIFICATION;
        }

        $base = array_merge($base, [
            self::ORDER_WAITING_CONFIRMATION,
            self::ORDER_PROCESSING,
            self::ORDER_PREPARED,
        ]);

        $base[] = $order?->metode_pengambilan === 'kurir_toko'
            ? self::ORDER_DELIVERING
            : self::ORDER_READY_PICKUP;

        $base[] = self::ORDER_DONE;

        return $base;
    }

    public static function shippingSteps(?Pengiriman $shipment): array
    {
        return $shipment?->metode === 'kurir_toko'
            ? [
                self::ORDER_PREPARED,
                self::ORDER_DELIVERING,
                self::ORDER_DONE,
            ]
            : [
                self::ORDER_PREPARED,
                self::ORDER_READY_PICKUP,
                self::ORDER_DONE,
            ];
    }
}