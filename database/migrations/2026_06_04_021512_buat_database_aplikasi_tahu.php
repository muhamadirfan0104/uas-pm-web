<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ALAMAT
        |--------------------------------------------------------------------------
        */
        Schema::create('alamat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_penerima', 100);
            $table->string('telepon', 20);
            $table->string('email_penerima', 150)->nullable();
            $table->text('alamat_lengkap');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('utama')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'utama']);
        });

        /*
        |--------------------------------------------------------------------------
        | PRODUK
        |--------------------------------------------------------------------------
        */
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->decimal('harga', 10, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->integer('min_stok')->default(10);
            $table->string('satuan', 30);
            $table->integer('isi_per_satuan')->nullable();
            $table->decimal('berat', 8, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('masa_simpan')->nullable();
            $table->string('saran_penyimpanan')->nullable();
            $table->string('saran_penyajian')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['aktif', 'stok']);
        });

        /*
        |--------------------------------------------------------------------------
        | GAMBAR PRODUK
        |--------------------------------------------------------------------------
        */
        Schema::create('gambar_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->string('url_gambar');
            $table->boolean('utama')->default(false);
            $table->timestamps();

            $table->index(['produk_id', 'utama']);
        });

        /*
        |--------------------------------------------------------------------------
        | KERANJANG WEB
        |--------------------------------------------------------------------------
        */
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | ITEM KERANJANG WEB
        |--------------------------------------------------------------------------
        */
        Schema::create('item_keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keranjang_id')->constrained('keranjang')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['keranjang_id', 'produk_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | PESANAN
        |--------------------------------------------------------------------------
        */
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('alamat_pengiriman_id')->nullable()->constrained('alamat')->nullOnDelete();

            $table->string('nomor_invoice', 50)->unique();
            $table->dateTime('tanggal_pesanan');

            $table->decimal('subtotal_produk', 10, 2)->default(0);
            $table->decimal('jarak_km', 6, 2)->nullable();
            $table->decimal('biaya_pengiriman', 10, 2)->default(0);
            $table->decimal('total_bayar', 10, 2)->default(0);

            $table->enum('metode_pengambilan', [
                'ambil_toko',
                'kurir_toko',
            ]);

            $table->enum('status', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'menunggu_konfirmasi',
                'diproses',
                'disiapkan',
                'siap_diambil',
                'dalam_pengantaran',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran');

            $table->enum('status_pembayaran', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'dibayar',
                'ditolak',
                'gagal',
                'kedaluwarsa',
                'dibatalkan',
            ])->default('menunggu_pembayaran');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'tanggal_pesanan']);
            $table->index(['status_pembayaran', 'tanggal_pesanan']);
        });

        /*
        |--------------------------------------------------------------------------
        | ITEM PESANAN
        |--------------------------------------------------------------------------
        */
        Schema::create('item_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();

            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);

            $table->timestamps();

            $table->index(['pesanan_id', 'produk_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->unique()->constrained('pesanan')->cascadeOnDelete();

            $table->enum('metode_pembayaran', [
                'cod',
                'transfer_bank',
                'qris',
                'tunai',
            ])->default('transfer_bank');

            $table->string('referensi_pembayaran', 100)->nullable();
            $table->decimal('jumlah', 10, 2)->default(0);

            $table->enum('status', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'dibayar',
                'ditolak',
                'gagal',
                'kedaluwarsa',
                'dibatalkan',
            ])->default('menunggu_pembayaran');

            $table->string('tautan_pembayaran')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->text('catatan_admin')->nullable();

            $table->dateTime('dibayar_pada')->nullable();
            $table->dateTime('diverifikasi_pada')->nullable();

            $table->timestamps();

            $table->index(['status', 'metode_pembayaran', 'created_at'], 'idx_pembayaran_status_metode_tanggal');
        });

        /*
        |--------------------------------------------------------------------------
        | PENGIRIMAN / PENGAMBILAN
        |--------------------------------------------------------------------------
        */
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->unique()->constrained('pesanan')->cascadeOnDelete();

            $table->enum('metode', [
                'ambil_toko',
                'kurir_toko',
            ])->default('ambil_toko');

            $table->enum('status_pengiriman', [
                'menunggu',
                'siap_diambil',
                'dalam_pengantaran',
                'selesai',
            ])->default('menunggu');

            $table->text('alamat_toko')->nullable();
            $table->text('alamat_tujuan')->nullable();

            $table->decimal('latitude_tujuan', 10, 7)->nullable();
            $table->decimal('longitude_tujuan', 10, 7)->nullable();
            $table->decimal('jarak_km', 6, 2)->nullable();
            $table->decimal('biaya', 10, 2)->default(0);

            $table->timestamps();

            $table->index(['status_pengiriman', 'metode']);
        });

        /*
        |--------------------------------------------------------------------------
        | ULASAN
        |--------------------------------------------------------------------------
        | media_ulasan TIDAK dibuat di sini.
        | media_ulasan tetap dibuat di migration sendiri.
        |--------------------------------------------------------------------------
        */
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->tinyInteger('rating');
            $table->text('komentar')->nullable();

            $table->string('foto_ulasan')->nullable();
            $table->string('video_ulasan')->nullable();

            $table->boolean('ditampilkan')->default(true);

            $table->timestamps();

            $table->unique(['pesanan_id', 'produk_id', 'user_id']);
            $table->index(['produk_id', 'rating', 'ditampilkan', 'created_at'], 'idx_ulasan_produk_rating_status');
        });

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT STOK
        |--------------------------------------------------------------------------
        */
        Schema::create('riwayat_stok', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();

            $table->integer('perubahan');
            $table->enum('tipe', [
                'tambah',
                'kurang',
                'penyesuaian',
                'checkout',
            ]);

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['produk_id', 'tipe', 'created_at']);
        });

        /*
        |--------------------------------------------------------------------------
        | PENGATURAN TOKO
        |--------------------------------------------------------------------------
        */
        Schema::create('pengaturan_toko', function (Blueprint $table) {
            $table->id();

            $table->string('nama', 100)->nullable();
            $table->string('logo_url')->nullable();
            $table->text('alamat')->nullable();

            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();

            $table->string('jam_buka', 50)->nullable();
            $table->string('jam_tutup', 50)->nullable();

            $table->decimal('latitude_toko', 10, 7)->nullable();
            $table->decimal('longitude_toko', 10, 7)->nullable();

            $table->decimal('tarif_per_km', 10, 2)->default(0);
            $table->decimal('biaya_minimum_pengiriman', 10, 2)->default(0);
            $table->decimal('radius_maksimal_km', 6, 2)->nullable();

            $table->string('area_pengiriman')->nullable();
            $table->text('info_pembayaran')->nullable();

            $table->string('bank_nama', 100)->nullable();
            $table->string('bank_nomor_rekening', 60)->nullable();
            $table->string('bank_atas_nama', 120)->nullable();

            $table->text('tentang')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | CATATAN:
        |--------------------------------------------------------------------------
        | Tabel favorit, notifikasi, dan banner sengaja TIDAK dibuat.
        | Tabel media_ulasan tetap dibuat di migration terpisah.
        |--------------------------------------------------------------------------
        */
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_toko');
        Schema::dropIfExists('riwayat_stok');
        Schema::dropIfExists('ulasan');
        Schema::dropIfExists('pengiriman');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('item_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('item_keranjang');
        Schema::dropIfExists('keranjang');
        Schema::dropIfExists('gambar_produk');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('alamat');
    }
};