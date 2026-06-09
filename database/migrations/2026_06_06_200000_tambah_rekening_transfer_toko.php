<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pengaturan_toko')) {
            return;
        }

        Schema::table('pengaturan_toko', function (Blueprint $table) {
            if (! Schema::hasColumn('pengaturan_toko', 'bank_nama')) {
                $table->string('bank_nama', 100)->nullable()->after('info_pembayaran');
            }

            if (! Schema::hasColumn('pengaturan_toko', 'bank_nomor_rekening')) {
                $table->string('bank_nomor_rekening', 60)->nullable()->after('bank_nama');
            }

            if (! Schema::hasColumn('pengaturan_toko', 'bank_atas_nama')) {
                $table->string('bank_atas_nama', 120)->nullable()->after('bank_nomor_rekening');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pengaturan_toko')) {
            return;
        }

        Schema::table('pengaturan_toko', function (Blueprint $table) {
            foreach (['bank_atas_nama', 'bank_nomor_rekening', 'bank_nama'] as $column) {
                if (Schema::hasColumn('pengaturan_toko', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
