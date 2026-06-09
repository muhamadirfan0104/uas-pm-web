<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alamat') && ! Schema::hasColumn('alamat', 'email_penerima')) {
            Schema::table('alamat', function (Blueprint $table) {
                $table->string('email_penerima', 150)->nullable()->after('telepon');
            });
        }

        if (Schema::hasTable('pembayaran')) {
            DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('qris','tunai','cod','transfer_bank') NOT NULL DEFAULT 'transfer_bank'");
            DB::statement("ALTER TABLE pembayaran MODIFY status ENUM('menunggu_pembayaran','dibayar','ditolak','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");

            Schema::table('pembayaran', function (Blueprint $table) {
                if (! Schema::hasColumn('pembayaran', 'bukti_transfer')) {
                    $table->string('bukti_transfer')->nullable()->after('qr_code');
                }
                if (! Schema::hasColumn('pembayaran', 'catatan_admin')) {
                    $table->text('catatan_admin')->nullable()->after('bukti_transfer');
                }
                if (! Schema::hasColumn('pembayaran', 'diverifikasi_pada')) {
                    $table->dateTime('diverifikasi_pada')->nullable()->after('dibayar_pada');
                }
            });
        }

        if (Schema::hasTable('pesanan')) {
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu_pembayaran','dibayar','ditolak','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pembayaran')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                if (Schema::hasColumn('pembayaran', 'diverifikasi_pada')) {
                    $table->dropColumn('diverifikasi_pada');
                }
                if (Schema::hasColumn('pembayaran', 'catatan_admin')) {
                    $table->dropColumn('catatan_admin');
                }
                if (Schema::hasColumn('pembayaran', 'bukti_transfer')) {
                    $table->dropColumn('bukti_transfer');
                }
            });

            DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('qris','tunai') NOT NULL DEFAULT 'qris'");
            DB::statement("ALTER TABLE pembayaran MODIFY status ENUM('menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('pesanan')) {
            DB::statement("ALTER TABLE pesanan MODIFY status_pembayaran ENUM('menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran'");
        }

        if (Schema::hasTable('alamat') && Schema::hasColumn('alamat', 'email_penerima')) {
            Schema::table('alamat', function (Blueprint $table) {
                $table->dropColumn('email_penerima');
            });
        }
    }
};
