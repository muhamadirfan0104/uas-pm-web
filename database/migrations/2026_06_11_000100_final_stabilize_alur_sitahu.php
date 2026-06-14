<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('role', 'kasir')->update(['role' => 'admin']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','pembeli') NOT NULL DEFAULT 'pembeli'");
        }

        if (Schema::hasTable('pembayaran')) {
            /*
             * Normalisasi metode dan status lama sebelum enum dipersempit.
             */
            DB::table('pembayaran')->whereIn('metode_pembayaran', ['tunai', 'cash', 'kasir'])->update(['metode_pembayaran' => 'cod']);
            DB::table('pembayaran')->whereIn('metode_pembayaran', ['qris', 'va', 'ewallet', 'transfer'])->update(['metode_pembayaran' => 'transfer_bank']);
            DB::table('pembayaran')->whereIn('status', ['gagal', 'kedaluwarsa'])->update(['status' => 'ditolak']);

            DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('transfer_bank','cod') NOT NULL DEFAULT 'transfer_bank'");
            DB::statement("ALTER TABLE pembayaran MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('pesanan')) {
            DB::table('pesanan')->whereIn('status_pembayaran', ['gagal', 'kedaluwarsa'])->update(['status_pembayaran' => 'ditolak']);

            /*
             * Alur final tidak memakai status menunggu_konfirmasi.
             * Begitu COD dibuat atau transfer diterima, pesanan masuk Diproses.
             */
            DB::table('pesanan')->where('status', 'menunggu_konfirmasi')->update(['status' => 'diproses']);
            DB::table('pesanan')->where('status', 'dibayar')->update(['status' => 'diproses']);

            if (Schema::hasTable('pembayaran')) {
                DB::table('pesanan')
                    ->whereIn('id', function ($query) {
                        $query->select('pesanan_id')
                            ->from('pembayaran')
                            ->where('metode_pembayaran', 'cod');
                    })
                    ->whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi'])
                    ->update([
                        'status' => 'diproses',
                        'status_pembayaran' => 'menunggu_pembayaran',
                    ]);

                DB::table('pesanan')
                    ->whereIn('id', function ($query) {
                        $query->select('pesanan_id')
                            ->from('pembayaran')
                            ->where('metode_pembayaran', 'transfer_bank')
                            ->where('status', 'dibayar');
                    })
                    ->whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi'])
                    ->update([
                        'status' => 'diproses',
                        'status_pembayaran' => 'dibayar',
                    ]);

                DB::table('pesanan')
                    ->whereIn('id', function ($query) {
                        $query->select('pesanan_id')
                            ->from('pembayaran')
                            ->where('metode_pembayaran', 'transfer_bank')
                            ->where('status', 'menunggu_verifikasi');
                    })
                    ->whereNotIn('status', ['selesai', 'dibatalkan'])
                    ->update([
                        'status' => 'menunggu_verifikasi',
                        'status_pembayaran' => 'menunggu_verifikasi',
                    ]);
            }

            DB::statement("ALTER TABLE pesanan MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','diproses','disiapkan','siap_diambil','dalam_pengantaran','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('pengiriman')) {
            DB::statement("ALTER TABLE pengiriman MODIFY metode ENUM('ambil_toko','kurir_toko') NOT NULL DEFAULT 'ambil_toko'");
            DB::statement("ALTER TABLE pengiriman MODIFY status_pengiriman ENUM('siap_diambil','dalam_pengantaran','selesai') NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pesanan')) {
            DB::statement("ALTER TABLE pesanan MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','menunggu_konfirmasi','diproses','disiapkan','siap_diambil','dalam_pengantaran','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }
    }
};
