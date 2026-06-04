<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk') && ! Schema::hasColumn('produk', 'min_stok')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->integer('min_stok')->default(20)->after('stok');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('produk') && Schema::hasColumn('produk', 'min_stok')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->dropColumn('min_stok');
            });
        }
    }
};
