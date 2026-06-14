<?php

use App\Http\Controllers\Admin\AkunController;
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
use App\Http\Controllers\WebPembeli\AlamatController as WebPembeliAlamatController;
use App\Http\Controllers\WebPembeli\AuthController as WebPembeliAuthController;
use App\Http\Controllers\WebPembeli\CheckoutController as WebPembeliCheckoutController;
use App\Http\Controllers\WebPembeli\HomeController as WebPembeliHomeController;
use App\Http\Controllers\WebPembeli\KeranjangController as WebPembeliKeranjangController;
use App\Http\Controllers\WebPembeli\PesananController as WebPembeliPesananController;
use App\Http\Controllers\WebPembeli\ProfilController as WebPembeliProfilController;
use App\Http\Controllers\WebPembeli\UlasanController as WebPembeliUlasanController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\PembeliWebMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HALAMAN AWAL
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('pembeli-web.home'));

/*
|--------------------------------------------------------------------------
| WEB PEMBELI
|--------------------------------------------------------------------------
*/
Route::prefix('pembeli-web')
    ->name('pembeli-web.')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Halaman publik pembeli
        |--------------------------------------------------------------------------
        */
        Route::get('/', [WebPembeliHomeController::class, 'home'])
            ->name('home');

        Route::get('/login', [WebPembeliAuthController::class, 'showLogin'])
            ->name('login');

        Route::post('/login', [WebPembeliAuthController::class, 'login'])
            ->name('login.post');

        Route::get('/register', [WebPembeliAuthController::class, 'showRegister'])
            ->name('register');

        Route::post('/register', [WebPembeliAuthController::class, 'register'])
            ->name('register.post');

        Route::post('/logout', [WebPembeliAuthController::class, 'logout'])
            ->middleware('auth')
            ->name('logout');

        Route::get('/produk', [WebPembeliHomeController::class, 'produk'])
            ->name('produk');

        Route::get('/ulasan', [WebPembeliHomeController::class, 'ulasan'])
            ->name('ulasan');

        Route::get('/produk/{produk}', [WebPembeliHomeController::class, 'detailProduk'])
            ->name('produk.detail');

        Route::post('/produk/{produk}/beli-sekarang', [WebPembeliCheckoutController::class, 'buyNow'])
            ->name('checkout.buy-now');

        Route::get('/coming-soon', [WebPembeliHomeController::class, 'comingSoon'])
            ->name('coming-soon');

        /*
        |--------------------------------------------------------------------------
        | Keranjang publik
        |--------------------------------------------------------------------------
        | Pembeli yang belum login tetap bisa menambahkan produk ke keranjang.
        | Setelah login, isi keranjang session akan disinkronkan ke database.
        */
        Route::get('/keranjang', [WebPembeliKeranjangController::class, 'index'])
            ->name('keranjang.index');

        Route::post('/keranjang/checkout', [WebPembeliKeranjangController::class, 'checkoutSelected'])
            ->name('keranjang.checkout');

        Route::post('/keranjang/{produk}', [WebPembeliKeranjangController::class, 'store'])
            ->name('keranjang.store');

        Route::patch('/keranjang/{produk}', [WebPembeliKeranjangController::class, 'update'])
            ->name('keranjang.update');

        Route::delete('/keranjang/{produk}', [WebPembeliKeranjangController::class, 'destroy'])
            ->name('keranjang.destroy');

        Route::delete('/keranjang', [WebPembeliKeranjangController::class, 'clear'])
            ->name('keranjang.clear');

        /*
        |--------------------------------------------------------------------------
        | Halaman pembeli yang wajib login sebagai pembeli
        |--------------------------------------------------------------------------
        */
        Route::middleware([PembeliWebMiddleware::class])->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Checkout
            |--------------------------------------------------------------------------
            */
            Route::get('/checkout', [WebPembeliCheckoutController::class, 'index'])
                ->name('checkout.index');

            Route::post('/checkout', [WebPembeliCheckoutController::class, 'store'])
                ->name('checkout.store');

            Route::get('/checkout/sukses/{pesanan}', [WebPembeliCheckoutController::class, 'success'])
                ->name('checkout.success');

            /*
            |--------------------------------------------------------------------------
            | Pesanan Pembeli
            |--------------------------------------------------------------------------
            */
            Route::get('/pesanan', [WebPembeliPesananController::class, 'index'])
                ->name('pesanan.index');

            /*
            |--------------------------------------------------------------------------
            | Ulasan Pembeli
            |--------------------------------------------------------------------------
            | Harus sebelum detail pesanan supaya route ulasan tidak ketangkep
            | sebagai detail pesanan biasa.
            */
            Route::get('/pesanan/{nomor_invoice}/ulasan/{produk}/buat', [WebPembeliUlasanController::class, 'create'])
                ->name('ulasan.create');

            Route::post('/pesanan/{nomor_invoice}/ulasan/{produk}', [WebPembeliUlasanController::class, 'store'])
                ->name('ulasan.store');

            Route::patch('/pesanan/{nomor_invoice}/batalkan', [WebPembeliPesananController::class, 'cancel'])
                ->name('pesanan.cancel');

            Route::post('/pesanan/{nomor_invoice}/bukti-transfer', [WebPembeliPesananController::class, 'uploadBuktiPembayaran'])
                ->name('pesanan.bukti-transfer');

            Route::patch('/pesanan/{nomor_invoice}/diterima', [WebPembeliPesananController::class, 'confirmReceived'])
                ->name('pesanan.confirm-received');

            Route::get('/pesanan/{nomor_invoice}/invoice', [WebPembeliPesananController::class, 'invoice'])
                ->name('pesanan.invoice');

            Route::get('/pesanan/{nomor_invoice}', [WebPembeliPesananController::class, 'show'])
                ->name('pesanan.show');

            /*
            |--------------------------------------------------------------------------
            | Alamat Pembeli
            |--------------------------------------------------------------------------
            */
            Route::get('/alamat', [WebPembeliAlamatController::class, 'index'])
                ->name('alamat.index');

            Route::get('/alamat/tambah', [WebPembeliAlamatController::class, 'create'])
                ->name('alamat.create');

            Route::post('/alamat', [WebPembeliAlamatController::class, 'store'])
                ->name('alamat.store');

            Route::get('/alamat/{alamat}/edit', [WebPembeliAlamatController::class, 'edit'])
                ->name('alamat.edit');

            Route::put('/alamat/{alamat}', [WebPembeliAlamatController::class, 'update'])
                ->name('alamat.update');

            Route::patch('/alamat/{alamat}/utama', [WebPembeliAlamatController::class, 'setUtama'])
                ->name('alamat.utama');

            Route::delete('/alamat/{alamat}', [WebPembeliAlamatController::class, 'destroy'])
                ->name('alamat.destroy');

            /*
            |--------------------------------------------------------------------------
            | Profil Pembeli
            |--------------------------------------------------------------------------
            */
            Route::get('/profil', [WebPembeliProfilController::class, 'show'])
                ->name('profil');

            Route::put('/profil', [WebPembeliProfilController::class, 'update'])
                ->name('profil.update');

            Route::put('/profil/password', [WebPembeliProfilController::class, 'updatePassword'])
                ->name('profil.password');
        });
    });

/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Akun Admin Login
        |--------------------------------------------------------------------------
        */
        Route::get('/akun', [AkunController::class, 'edit'])
            ->name('akun.edit');

        Route::patch('/akun', [AkunController::class, 'update'])
            ->name('akun.update');

        /*
        |--------------------------------------------------------------------------
        | Pengguna Admin
        |--------------------------------------------------------------------------
        */
        Route::patch('/pengguna-admin/{penggunaAdmin}/toggle', [PenggunaAdminController::class, 'toggle'])
            ->name('pengguna-admin.toggle');

        Route::resource('pengguna-admin', PenggunaAdminController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | Produk
        |--------------------------------------------------------------------------
        */
        Route::patch('/produk/{produk}/toggle', [ProdukController::class, 'toggle'])
            ->name('produk.toggle');

        Route::resource('produk', ProdukController::class)
            ->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | Stok
        |--------------------------------------------------------------------------
        */
        Route::get('/stok', [StokController::class, 'index'])
            ->name('stok.index');

        Route::patch('/stok/{produk}', [StokController::class, 'update'])
            ->name('stok.update');

        /*
        |--------------------------------------------------------------------------
        | Semua Pesanan / Arsip Invoice
        |--------------------------------------------------------------------------
        */
        Route::get('/semua-pesanan', [PesananController::class, 'semua'])
            ->name('semua-pesanan.index');

        /*
        |--------------------------------------------------------------------------
        | Pesanan Admin
        |--------------------------------------------------------------------------
        | Route invoice wajib di atas route show.
        */
        Route::get('/pesanan', [PesananController::class, 'index'])
            ->name('pesanan.index');

        Route::get('/pesanan/{pesanan}/invoice', [PesananController::class, 'invoice'])
            ->name('pesanan.invoice');

        Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])
            ->name('pesanan.show');

        Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])
            ->name('pesanan.status');

        /*
        |--------------------------------------------------------------------------
        | Pembayaran
        |--------------------------------------------------------------------------
        */
        Route::get('/pembayaran', [PembayaranController::class, 'index'])
            ->name('pembayaran.index');

        Route::patch('/pembayaran/{pembayaran}/status', [PembayaranController::class, 'updateStatus'])
            ->name('pembayaran.status');

        Route::patch('/pembayaran/{pembayaran}/terima', [PembayaranController::class, 'terima'])
            ->name('pembayaran.terima');

        Route::patch('/pembayaran/{pembayaran}/tolak', [PembayaranController::class, 'tolak'])
            ->name('pembayaran.tolak');

        /*
        |--------------------------------------------------------------------------
        | Pengiriman
        |--------------------------------------------------------------------------
        */
        Route::get('/pengiriman', [PengirimanController::class, 'index'])
            ->name('pengiriman.index');

        Route::put('/pengiriman/pengaturan', [PengirimanController::class, 'updatePengaturan'])
            ->name('pengiriman.pengaturan.update');

        Route::patch('/pengiriman/{pengiriman}/status', [PengirimanController::class, 'updateStatus'])
            ->name('pengiriman.status');

        /*
        |--------------------------------------------------------------------------
        | Pembeli
        |--------------------------------------------------------------------------
        */
        Route::get('/pembeli', [PembeliController::class, 'index'])
            ->name('pembeli.index');

        Route::get('/pembeli/{pembeli}', [PembeliController::class, 'show'])
            ->name('pembeli.show');

        Route::patch('/pembeli/{pembeli}/toggle', [PembeliController::class, 'toggle'])
            ->name('pembeli.toggle');

        /*
        |--------------------------------------------------------------------------
        | Ulasan
        |--------------------------------------------------------------------------
        */
        Route::get('/ulasan', [UlasanController::class, 'index'])
            ->name('ulasan.index');

        Route::patch('/ulasan/{ulasan}/toggle', [UlasanController::class, 'toggle'])
            ->name('ulasan.toggle');

        Route::delete('/ulasan/{ulasan}', [UlasanController::class, 'destroy'])
            ->name('ulasan.destroy');

        /*
        |--------------------------------------------------------------------------
        | Laporan
        |--------------------------------------------------------------------------
        */
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');

        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])
            ->name('laporan.export.excel');

        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])
            ->name('laporan.export.pdf');

        /*
        |--------------------------------------------------------------------------
        | Pengaturan Toko
        |--------------------------------------------------------------------------
        */
        Route::get('/pengaturan', [PengaturanTokoController::class, 'edit'])
            ->name('pengaturan.edit');

        Route::put('/pengaturan', [PengaturanTokoController::class, 'update'])
            ->name('pengaturan.update');
    });


/*
|--------------------------------------------------------------------------
| FALLBACK GLOBAL
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});