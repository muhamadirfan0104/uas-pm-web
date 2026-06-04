<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PembeliController;
use App\Http\Controllers\Admin\PengaturanTokoController;
use App\Http\Controllers\Admin\PengirimanController;
use App\Http\Controllers\Admin\PenggunaAdminController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\StokController;
use App\Http\Controllers\Admin\UlasanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\WebPembeli\HomeController as WebPembeliHomeController;
use App\Http\Controllers\WebPembeli\KeranjangController as WebPembeliKeranjangController;
use App\Http\Controllers\WebPembeli\CheckoutController as WebPembeliCheckoutController;
use App\Http\Controllers\WebPembeli\PesananController as WebPembeliPesananController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\KasirMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));


Route::prefix('pembeli-web')
    ->name('pembeli-web.')
    ->group(function () {
        Route::get('/', [WebPembeliHomeController::class, 'home'])->name('home');

        Route::get('/produk', [WebPembeliHomeController::class, 'produk'])->name('produk');
        Route::get('/produk/{produk}', [WebPembeliHomeController::class, 'detailProduk'])->name('produk.detail');

        Route::get('/keranjang', [WebPembeliKeranjangController::class, 'index'])->name('keranjang.index');
        Route::post('/keranjang/{produk}', [WebPembeliKeranjangController::class, 'store'])->name('keranjang.store');
        Route::patch('/keranjang/{produk}', [WebPembeliKeranjangController::class, 'update'])->name('keranjang.update');
        Route::delete('/keranjang/{produk}', [WebPembeliKeranjangController::class, 'destroy'])->name('keranjang.destroy');
        Route::delete('/keranjang', [WebPembeliKeranjangController::class, 'clear'])->name('keranjang.clear');

        Route::get('/checkout', [WebPembeliCheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [WebPembeliCheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/sukses/{pesanan}', [WebPembeliCheckoutController::class, 'success'])->name('checkout.success');

        Route::get('/pesanan', [WebPembeliPesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{nomor_invoice}', [WebPembeliPesananController::class, 'show'])->name('pesanan.show');

        Route::get('/profil', [WebPembeliHomeController::class, 'profil'])->name('profil');

        Route::get('/coming-soon', [WebPembeliHomeController::class, 'comingSoon'])->name('coming-soon');
    });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/akun', [AkunController::class, 'edit'])->name('akun.edit');
        Route::patch('/akun', [AkunController::class, 'update'])->name('akun.update');

        Route::patch('/pengguna-admin/{penggunaAdmin}/toggle', [PenggunaAdminController::class, 'toggle'])->name('pengguna-admin.toggle');
        Route::resource('pengguna-admin', PenggunaAdminController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::patch('/produk/{produk}/toggle', [ProdukController::class, 'toggle'])->name('produk.toggle');
        Route::resource('produk', ProdukController::class)->except(['show']);

        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::patch('/stok/{produk}', [StokController::class, 'update'])->name('stok.update');

        Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');

        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::patch('/pembayaran/{pembayaran}/status', [PembayaranController::class, 'updateStatus'])->name('pembayaran.status');

        Route::get('/pengiriman', [PengirimanController::class, 'index'])->name('pengiriman.index');
        Route::put('/pengiriman/pengaturan', [PengirimanController::class, 'updatePengaturan'])->name('pengiriman.pengaturan.update');
        Route::patch('/pengiriman/{pengiriman}/status', [PengirimanController::class, 'updateStatus'])->name('pengiriman.status');

        Route::get('/pembeli', [PembeliController::class, 'index'])->name('pembeli.index');
        Route::get('/pembeli/{pembeli}', [PembeliController::class, 'show'])->name('pembeli.show');
        Route::patch('/pembeli/{pembeli}/toggle', [PembeliController::class, 'toggle'])->name('pembeli.toggle');

        Route::get('/ulasan', [UlasanController::class, 'index'])->name('ulasan.index');
        Route::patch('/ulasan/{ulasan}/toggle', [UlasanController::class, 'toggle'])->name('ulasan.toggle');
        Route::delete('/ulasan/{ulasan}', [UlasanController::class, 'destroy'])->name('ulasan.destroy');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export.csv');

        Route::get('/pengaturan', [PengaturanTokoController::class, 'edit'])->name('pengaturan.edit');
        Route::put('/pengaturan', [PengaturanTokoController::class, 'update'])->name('pengaturan.update');

        Route::patch('/banner/{banner}/toggle', [BannerController::class, 'toggle'])->name('banner.toggle');
        Route::resource('banner', BannerController::class)->except(['show']);

        Route::fallback(function () {
            return response()->view('errors.admin-404', [], 404);
        })->name('not-found');
    });

Route::middleware(['auth', KasirMiddleware::class])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

        Route::fallback(function () {
            return response()->view('errors.kasir-404', [], 404);
        })->name('not-found');
    });

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
