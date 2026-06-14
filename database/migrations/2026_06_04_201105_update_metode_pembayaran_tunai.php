<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('transfer_bank','cod') NOT NULL DEFAULT 'transfer_bank'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('transfer_bank','cod') NOT NULL DEFAULT 'transfer_bank'");
    }
};
