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
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','kasir','pembeli') NOT NULL DEFAULT 'pembeli'");
        }
    }
};
