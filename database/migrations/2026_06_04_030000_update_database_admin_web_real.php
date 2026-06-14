<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Role sistem disederhanakan: admin dan pembeli
        |--------------------------------------------------------------------------
        */
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')
                ->where('role', 'kasir')
                ->update(['role' => 'admin']);

            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','pembeli') NOT NULL DEFAULT 'pembeli'");
        }

        /*
        |--------------------------------------------------------------------------
        | Status aktif user
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'aktif')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('aktif')->default(true)->after('role');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status tampil ulasan
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('ulasan') && ! Schema::hasColumn('ulasan', 'ditampilkan')) {
            Schema::table('ulasan', function (Blueprint $table) {
                $table->boolean('ditampilkan')->default(true)->after('video_ulasan');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Jam tutup toko
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('pengaturan_toko') && ! Schema::hasColumn('pengaturan_toko', 'jam_tutup')) {
            Schema::table('pengaturan_toko', function (Blueprint $table) {
                $table->string('jam_tutup', 50)->nullable()->after('jam_buka');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | CATATAN:
        |--------------------------------------------------------------------------
        | Bagian update tabel banner dihapus karena fitur banner tidak dipakai.
        |--------------------------------------------------------------------------
        */
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','pembeli') NOT NULL DEFAULT 'pembeli'");
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'aktif')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('aktif');
            });
        }

        if (Schema::hasTable('ulasan') && Schema::hasColumn('ulasan', 'ditampilkan')) {
            Schema::table('ulasan', function (Blueprint $table) {
                $table->dropColumn('ditampilkan');
            });
        }

        if (Schema::hasTable('pengaturan_toko') && Schema::hasColumn('pengaturan_toko', 'jam_tutup')) {
            Schema::table('pengaturan_toko', function (Blueprint $table) {
                $table->dropColumn('jam_tutup');
            });
        }
    }
};