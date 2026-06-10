<?php

use App\Http\Controllers\Api\Admin\DashboardApiController;
use App\Http\Controllers\Api\Admin\PembayaranApiController as AdminPembayaranApiController;
use App\Http\Controllers\Api\Admin\PesananApiController as AdminPesananApiController;
use App\Http\Controllers\Api\Admin\ProdukApiController as AdminProdukApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\Pembeli\AlamatApiController;
use App\Http\Controllers\Api\Pembeli\KeranjangApiController;
use App\Http\Controllers\Api\Pembeli\PesananApiController;
use App\Http\Controllers\Api\Pembeli\UlasanApiController;
use App\Http\Controllers\Api\PublicApiController;
use App\Http\Middleware\ApiTokenMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/health', [PublicApiController::class, 'health']);
Route::get('/store', [PublicApiController::class, 'store']);
Route::get('/banners', [PublicApiController::class, 'banners']);
Route::get('/products', [PublicApiController::class, 'products']);
Route::get('/products/{produk}', [PublicApiController::class, 'product']);
Route::get('/products/{produk}/reviews', [PublicApiController::class, 'productReviews']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
});

Route::middleware(ApiTokenMiddleware::class)->group(function () {
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::put('/profile', [AuthApiController::class, 'updateProfile']);
    Route::put('/password', [AuthApiController::class, 'changePassword']);
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);

    Route::apiResource('/addresses', AlamatApiController::class)->except(['show']);
    Route::patch('/addresses/{alamat}/main', [AlamatApiController::class, 'setUtama']);

    Route::get('/cart', [KeranjangApiController::class, 'index']);
    Route::post('/cart/items', [KeranjangApiController::class, 'store']);
    Route::patch('/cart/items/{itemKeranjang}', [KeranjangApiController::class, 'update']);
    Route::delete('/cart/items/{itemKeranjang}', [KeranjangApiController::class, 'destroy']);
    Route::delete('/cart', [KeranjangApiController::class, 'clear']);

    Route::get('/orders', [PesananApiController::class, 'index']);
    Route::post('/checkout', [PesananApiController::class, 'checkoutFromCart']);
    Route::post('/orders', [PesananApiController::class, 'store']);
    Route::get('/orders/{pesanan}', [PesananApiController::class, 'show']);
    Route::patch('/orders/{pesanan}/cancel', [PesananApiController::class, 'cancel']);
    Route::patch('/orders/{pesanan}/received', [PesananApiController::class, 'confirmReceived']);

    Route::post('/reviews', [UlasanApiController::class, 'store']);
    Route::get('/reviews/me', [UlasanApiController::class, 'myReviews']);

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', DashboardApiController::class);
        Route::apiResource('/products', AdminProdukApiController::class);
        Route::get('/orders', [AdminPesananApiController::class, 'index']);
        Route::get('/orders/{pesanan}', [AdminPesananApiController::class, 'show']);
        Route::patch('/orders/{pesanan}/status', [AdminPesananApiController::class, 'updateStatus']);
        Route::get('/payments', [AdminPembayaranApiController::class, 'index']);
        Route::patch('/payments/{pembayaran}/status', [AdminPembayaranApiController::class, 'updateStatus']);
    });
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint API tidak ditemukan.',
    ], 404);
});