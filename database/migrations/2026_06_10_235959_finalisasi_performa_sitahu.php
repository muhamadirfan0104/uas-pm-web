<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('role', 'kasir')->update(['role' => 'admin']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','pembeli') NOT NULL DEFAULT 'pembeli'");
        }

        if (Schema::hasTable('pembayaran')) {
            DB::table('pembayaran')->whereIn('metode_pembayaran', ['tunai'])->update(['metode_pembayaran' => 'cod']);
            DB::table('pembayaran')->whereIn('metode_pembayaran', ['qris', 'va', 'ewallet'])->update(['metode_pembayaran' => 'transfer_bank']);
            DB::table('pembayaran')->whereIn('status', ['gagal', 'kedaluwarsa'])->update(['status' => 'ditolak']);
            DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('transfer_bank','cod') NOT NULL DEFAULT 'transfer_bank'");
            DB::statement("ALTER TABLE pembayaran MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('pesanan')) {
            DB::table('pesanan')->where('status', 'dibayar')->update(['status' => 'menunggu_konfirmasi']);
            DB::table('pesanan')->whereIn('status_pembayaran', ['gagal', 'kedaluwarsa'])->update(['status_pembayaran' => 'ditolak']);
            DB::statement("ALTER TABLE pesanan MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','menunggu_konfirmasi','diproses','disiapkan','siap_diambil','dalam_pengantaran','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('pengiriman')) {
            DB::statement("ALTER TABLE pengiriman MODIFY metode ENUM('ambil_toko','kurir_toko') NOT NULL DEFAULT 'ambil_toko'");
            DB::statement("ALTER TABLE pengiriman MODIFY status_pengiriman ENUM('siap_diambil','dalam_pengantaran','selesai') NULL");
        }

        $this->index('pesanan', 'idx_pesanan_status_tanggal', 'status, tanggal_pesanan');
        $this->index('pesanan', 'idx_pesanan_bayar_tanggal', 'status_pembayaran, tanggal_pesanan');
        $this->index('pembayaran', 'idx_pembayaran_status_metode_tanggal', 'status, metode_pembayaran, created_at');
        $this->index('pengiriman', 'idx_pengiriman_status_metode', 'status_pengiriman, metode');
        $this->index('ulasan', 'idx_ulasan_produk_rating_status', 'produk_id, rating, ditampilkan, created_at');
        $this->index('produk', 'idx_produk_aktif_stok', 'aktif, stok');
        $this->index('alamat', 'idx_alamat_user_utama', 'user_id, utama');
    }

    public function down(): void
    {
        $this->dropIndex('pesanan', 'idx_pesanan_status_tanggal');
        $this->dropIndex('pesanan', 'idx_pesanan_bayar_tanggal');
        $this->dropIndex('pembayaran', 'idx_pembayaran_status_metode_tanggal');
        $this->dropIndex('pengiriman', 'idx_pengiriman_status_metode');
        $this->dropIndex('ulasan', 'idx_ulasan_produk_rating_status');
        $this->dropIndex('produk', 'idx_produk_aktif_stok');
        $this->dropIndex('alamat', 'idx_alamat_user_utama');
    }

    private function index(string $table, string $name, string $columns): void
    {
        if (! Schema::hasTable($table) || $this->hasIndex($table, $name)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} ADD INDEX {$name} ({$columns})");
    }

    private function dropIndex(string $table, string $name): void
    {
        if (Schema::hasTable($table) && $this->hasIndex($table, $name)) {
            DB::statement("ALTER TABLE {$table} DROP INDEX {$name}");
        }
    }

    private function hasIndex(string $table, string $name): bool
    {
        $database = DB::getDatabaseName();

        return collect(DB::select(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$database, $table, $name]
        ))->isNotEmpty();
    }
};
