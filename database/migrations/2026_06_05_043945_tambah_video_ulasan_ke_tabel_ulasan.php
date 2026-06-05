<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ulasan') && ! Schema::hasColumn('ulasan', 'video_ulasan')) {
            Schema::table('ulasan', function (Blueprint $table) {
                $table->string('video_ulasan')->nullable()->after('foto_ulasan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ulasan') && Schema::hasColumn('ulasan', 'video_ulasan')) {
            Schema::table('ulasan', function (Blueprint $table) {
                $table->dropColumn('video_ulasan');
            });
        }
    }
};