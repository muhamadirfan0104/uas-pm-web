<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'api_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('api_token', 100)->nullable()->unique()->after('remember_token');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'api_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['api_token']);
                $table->dropColumn('api_token');
            });
        }
    }
};
