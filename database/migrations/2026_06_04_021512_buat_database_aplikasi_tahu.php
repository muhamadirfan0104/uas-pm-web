<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------
        // Tabel Users (Laravel Bawaan)
        // -------------------------
        // Tabel Alamat
        // -------------------------
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
        });

        // -------------------------
        // Tabel Produk
        // -------------------------
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->decimal('harga', 10, 2);
            $table->integer('stok')->default(0);
            $table->string('satuan', 30);
            $table->integer('isi_per_satuan')->nullable();
            $table->decimal('berat', 8, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('masa_simpan')->nullable();
            $table->string('saran_penyimpanan')->nullable();
            $table->string('saran_penyajian')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // -------------------------
        // Tabel Gambar Produk
        // -------------------------
        Schema::create('gambar_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->string('url_gambar');
            $table->boolean('utama')->default(false);
            $table->timestamps();
        });

        // -------------------------
        // Tabel Keranjang
        // -------------------------
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // -------------------------
        // Tabel Item Keranjang
        // -------------------------
        Schema::create('item_keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keranjang_id')->constrained('keranjang')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // -------------------------
        // Tabel Pesanan
        // -------------------------
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nomor_invoice', 50)->unique();
            $table->dateTime('tanggal_pesanan');
            $table->decimal('subtotal_produk', 10, 2);
            $table->decimal('jarak_km', 6, 2)->nullable();
            $table->decimal('biaya_pengiriman', 10, 2)->default(0);
            $table->decimal('total_bayar', 10, 2);
            $table->enum('metode_pengambilan', ['ambil_toko', 'kurir_toko']);
            $table->foreignId('alamat_pengiriman_id')->nullable()->constrained('alamat')->nullOnDelete();
            $table->enum('status', [
                'menunggu_pembayaran',
                'dibayar',
                'diproses',
                'siap_diambil',
                'dalam_pengantaran',
                'selesai',
                'dibatalkan'
            ])->default('menunggu_pembayaran');
            $table->enum('status_pembayaran', [
                'menunggu_pembayaran',
                'dibayar',
                'gagal',
                'kedaluwarsa',
                'dibatalkan'
            ])->default('menunggu_pembayaran');
            $table->timestamps();
        });

        // -------------------------
        // Tabel Item Pesanan
        // -------------------------
        Schema::create('item_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // -------------------------
        // Tabel Pembayaran
        // -------------------------
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->enum('metode_pembayaran', ['transfer_bank', 'cod'])->default('transfer_bank');
            $table->string('referensi_pembayaran', 100)->nullable();
            $table->decimal('jumlah', 10, 2);
            $table->enum('status', ['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'])->default('menunggu_pembayaran');
            $table->string('tautan_pembayaran')->nullable();
            $table->string('qr_code')->nullable();
            $table->dateTime('dibayar_pada')->nullable();
            $table->timestamps();
        });

        // -------------------------
        // Tabel Pengiriman
        // -------------------------
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->enum('metode', ['ambil_toko','kurir_toko']);
            $table->enum('status_pengiriman', ['siap_diambil','dalam_pengantaran','selesai'])->nullable();
            $table->text('alamat_toko')->nullable();
            $table->text('alamat_tujuan')->nullable();
            $table->decimal('latitude_tujuan', 10, 7)->nullable();
            $table->decimal('longitude_tujuan', 10, 7)->nullable();
            $table->decimal('jarak_km', 6, 2)->nullable();
            $table->decimal('biaya', 10, 2)->default(0);
            $table->timestamps();
        });

        // -------------------------
        // Tabel Ulasan
        // -------------------------
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->text('komentar')->nullable();
            $table->string('foto_ulasan')->nullable();
            $table->timestamps();
        });

        // -------------------------
        // Tabel Favorit
        // -------------------------
        Schema::create('favorit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->timestamps();
        });

        // -------------------------
        // Tabel Notifikasi
        // -------------------------
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis', 50)->nullable();
            $table->text('pesan');
            $table->boolean('dibaca')->default(false);
            $table->timestamps();
        });

        // -------------------------
        // Tabel Riwayat Stok
        // -------------------------
        Schema::create('riwayat_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->integer('perubahan');
            $table->enum('tipe', ['tambah','kurang','penyesuaian']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // -------------------------
        // Tabel Pengaturan Toko
        // -------------------------
        Schema::create('pengaturan_toko', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->nullable();
            $table->string('logo_url')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('jam_buka', 50)->nullable();
            $table->decimal('latitude_toko', 10, 7)->nullable();
            $table->decimal('longitude_toko', 10, 7)->nullable();
            $table->decimal('tarif_per_km', 10, 2)->default(0);
            $table->decimal('biaya_minimum_pengiriman', 10, 2)->default(0);
            $table->decimal('radius_maksimal_km', 6, 2)->nullable();
            $table->string('area_pengiriman')->nullable();
            $table->text('info_pembayaran')->nullable();
            $table->text('tentang')->nullable();
            $table->timestamps();
        });

        // -------------------------
        // Tabel Banner (Opsional)
        // -------------------------
        Schema::create('banner', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 100);
            $table->string('url_gambar');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner');
        Schema::dropIfExists('pengaturan_toko');
        Schema::dropIfExists('riwayat_stok');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('favorit');
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