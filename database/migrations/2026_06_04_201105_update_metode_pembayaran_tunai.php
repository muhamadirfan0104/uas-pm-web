<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('qris', 'tunai') NOT NULL DEFAULT 'qris'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('qris', 'va', 'ewallet') NOT NULL DEFAULT 'qris'");
    }
};
