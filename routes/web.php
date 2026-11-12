<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StoreSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/produk/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/kontak', [CatalogController::class, 'contact'])->name('store.contact');
Route::get('/tentang', [CatalogController::class, 'about'])->name('store.about');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });
    Route::middleware('auth')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::resource('produk', ProductController::class)->parameters(['produk' => 'product'])->names('products')->except('show');
        Route::get('/pengaturan', [StoreSettingController::class, 'edit'])->name('settings.edit');
        Route::patch('/pengaturan', [StoreSettingController::class, 'update'])->name('settings.update');
    });
});
