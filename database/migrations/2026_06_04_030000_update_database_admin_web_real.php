<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supaya role web bisa dipakai oleh admin dan kasir, sedangkan pembeli tetap untuk mobile.
        if (Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','kasir','pembeli') NOT NULL DEFAULT 'pembeli'");
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'aktif')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('aktif')->default(true)->after('role');
            });
        }

        if (Schema::hasTable('ulasan') && ! Schema::hasColumn('ulasan', 'ditampilkan')) {
            Schema::table('ulasan', function (Blueprint $table) {
                $table->boolean('ditampilkan')->default(true)->after('foto_ulasan');
            });
        }

        if (Schema::hasTable('banner')) {
            Schema::table('banner', function (Blueprint $table) {
                if (! Schema::hasColumn('banner', 'deskripsi')) {
                    $table->text('deskripsi')->nullable()->after('judul');
                }
                if (! Schema::hasColumn('banner', 'urutan')) {
                    $table->unsignedInteger('urutan')->default(0)->after('aktif');
                }
            });
        }

        if (Schema::hasTable('pengaturan_toko') && ! Schema::hasColumn('pengaturan_toko', 'jam_tutup')) {
            Schema::table('pengaturan_toko', function (Blueprint $table) {
                $table->string('jam_tutup', 50)->nullable()->after('jam_buka');
            });
        }
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

        if (Schema::hasTable('banner')) {
            Schema::table('banner', function (Blueprint $table) {
                if (Schema::hasColumn('banner', 'deskripsi')) {
                    $table->dropColumn('deskripsi');
                }
                if (Schema::hasColumn('banner', 'urutan')) {
                    $table->dropColumn('urutan');
                }
            });
        }

        if (Schema::hasTable('pengaturan_toko') && Schema::hasColumn('pengaturan_toko', 'jam_tutup')) {
            Schema::table('pengaturan_toko', function (Blueprint $table) {
                $table->dropColumn('jam_tutup');
            });
        }
    }
};
