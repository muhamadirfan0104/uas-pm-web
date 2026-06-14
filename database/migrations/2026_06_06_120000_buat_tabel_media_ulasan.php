<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('media_ulasan')) {
            Schema::create('media_ulasan', function (Blueprint $table) {
                $table->id();

                $table->foreignId('ulasan_id')
                    ->constrained('ulasan')
                    ->cascadeOnDelete();

                $table->enum('jenis', ['foto', 'video']);
                $table->string('path');
                $table->string('caption')->nullable();
                $table->unsignedInteger('urutan')->default(0);

                $table->timestamps();

                $table->index(['ulasan_id', 'jenis']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('media_ulasan');
    }
};