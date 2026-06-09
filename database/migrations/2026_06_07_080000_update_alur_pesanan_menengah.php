<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pesanan')) {
            DB::statement("ALTER TABLE pesanan MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','menunggu_konfirmasi','dibayar','diproses','disiapkan','siap_diambil','dalam_pengantaran','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('pembayaran')) {
            DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('qris','va','ewallet','tunai','transfer_bank','cod') NOT NULL");
            DB::statement("ALTER TABLE pembayaran MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pesanan')) {
            DB::table('pesanan')->where('status', 'menunggu_verifikasi')->update(['status' => 'menunggu_pembayaran']);
            DB::table('pesanan')->where('status', 'menunggu_konfirmasi')->update(['status' => 'diproses']);
            DB::table('pesanan')->where('status', 'disiapkan')->update(['status' => 'diproses']);
            DB::table('pesanan')->where('status_pembayaran', 'menunggu_verifikasi')->update(['status_pembayaran' => 'menunggu_pembayaran']);
            DB::statement("ALTER TABLE pesanan MODIFY status ENUM('menunggu_pembayaran','dibayar','diproses','siap_diambil','dalam_pengantaran','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu_pembayaran','dibayar','ditolak','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('pembayaran')) {
            DB::table('pembayaran')->where('status', 'menunggu_verifikasi')->update(['status' => 'menunggu_pembayaran']);
            DB::statement("ALTER TABLE pembayaran MODIFY status ENUM('menunggu_pembayaran','dibayar','ditolak','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }
    }
};
