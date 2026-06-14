<?php

use App\Models\PengaturanToko;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rekening_toko')) {
            Schema::create('rekening_toko', function (Blueprint $table) {
                $table->id();
                $table->string('nama_bank', 100);
                $table->string('nomor_rekening', 80);
                $table->string('atas_nama', 150);
                $table->boolean('aktif')->default(true)->index();
                $table->boolean('utama')->default(false)->index();
                $table->unsignedInteger('urutan')->default(1)->index();
                $table->timestamps();

                $table->index(['aktif', 'utama', 'urutan']);
            });
        }

        if (Schema::hasTable('pengaturan_toko') && Schema::hasTable('rekening_toko')) {
            $sudahAda = DB::table('rekening_toko')->exists();
            $pengaturan = DB::table('pengaturan_toko')->first();

            if (! $sudahAda && $pengaturan) {
                $namaBank = trim((string) ($pengaturan->bank_nama ?? ''));
                $nomor = trim((string) ($pengaturan->bank_nomor_rekening ?? ''));
                $atasNama = trim((string) ($pengaturan->bank_atas_nama ?? ''));

                if ($namaBank !== '' || $nomor !== '' || $atasNama !== '') {
                    DB::table('rekening_toko')->insert([
                        'nama_bank' => $namaBank !== '' ? $namaBank : 'Bank',
                        'nomor_rekening' => $nomor !== '' ? $nomor : '-',
                        'atas_nama' => $atasNama !== '' ? $atasNama : ($pengaturan->nama ?? 'SiTahu'),
                        'aktif' => true,
                        'utama' => true,
                        'urutan' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rekening_toko');
    }
};
