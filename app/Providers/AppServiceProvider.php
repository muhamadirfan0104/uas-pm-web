<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.sitahu');
        Paginator::defaultSimpleView('vendor.pagination.sitahu-simple');

        View::share('rupiah', function ($value): string {
            return 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
        });

        View::share('statusClass', function ($status): string {
            return match ((string) $status) {
                'dibayar', 'selesai', 'aktif', 'masuk' => 'c-green',
                'diproses', 'disiapkan', 'siap_diambil', 'dalam_pengantaran', 'menunggu_konfirmasi' => 'c-purple',
                'menunggu_pembayaran', 'menunggu_verifikasi', 'menipis' => 'c-yellow',
                'gagal', 'kedaluwarsa', 'dibatalkan', 'ditolak', 'habis', 'keluar' => 'c-red',
                default => 'c-gray',
            };
        });

        View::share('statusLabel', function ($status): string {
            return match ((string) $status) {
                'menunggu_pembayaran' => 'Belum bayar',
                'menunggu_verifikasi' => 'Menunggu verifikasi',
                'menunggu_konfirmasi' => 'Menunggu konfirmasi',
                'diproses' => 'Diproses',
                'disiapkan' => 'Disiapkan',
                'siap_diambil' => 'Siap diambil',
                'dalam_pengantaran' => 'Dalam pengantaran',
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
                'ditolak' => 'Ditolak',
                'dibayar' => 'Dibayar',
                'gagal' => 'Gagal',
                'kedaluwarsa' => 'Kedaluwarsa',
                default => ucwords(str_replace('_', ' ', (string) $status)),
            };
        });
    }
}
