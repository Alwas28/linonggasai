<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\Public;
use App\Http\Controllers\Seller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public pages (blueprint 2.1) — no login required
|--------------------------------------------------------------------------
*/
Route::get('/', Public\HomeController::class)->name('home');
Route::get('/produk', [Public\ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{product:slug}', [Public\ProductController::class, 'show'])->name('products.show');
Route::get('/kategori/{category:slug}', [Public\ProductController::class, 'category'])->name('categories.show');
Route::get('/toko/{store:slug}', [Public\StoreController::class, 'show'])->name('stores.show');

foreach (['tentang' => 'about', 'cara-pesan' => 'how-to-order', 'lokasi' => 'location', 'kontak' => 'contact'] as $slug => $name) {
    Route::get('/'.$slug, [Public\PageController::class, 'show'])->defaults('slug', $slug)->name('pages.'.$name);
}

require __DIR__.'/auth.php';

Route::middleware(['auth', 'active'])->group(function () {
    Route::put('/kata-sandi', [PasswordController::class, 'update'])->name('password.change');

    /*
    |--------------------------------------------------------------------------
    | Seller dashboard (blueprint 2.2)
    |--------------------------------------------------------------------------
    */
    Route::prefix('penjual')->name('seller.')->middleware('role:seller')->group(function () {
        Route::get('/dashboard', Seller\DashboardController::class)->name('dashboard');
        Route::get('/profil', [Seller\AccountController::class, 'edit'])->name('account');
        Route::put('/profil', [Seller\AccountController::class, 'update'])->name('account.update');
        Route::get('/pengaturan', [Seller\AccountController::class, 'settings'])->name('settings');

        Route::middleware('seller.approved')->group(function () {
            Route::get('/profil-toko', [Seller\StoreProfileController::class, 'edit'])->name('store.edit');
            Route::put('/profil-toko', [Seller\StoreProfileController::class, 'update'])->name('store.update');
            Route::patch('/profil-toko/status', [Seller\StoreProfileController::class, 'toggle'])->name('store.toggle');

            Route::get('/produk', [Seller\ProductController::class, 'index'])->name('products.index');
            Route::get('/produk/tambah', [Seller\ProductController::class, 'create'])->name('products.create');
            Route::post('/produk', [Seller\ProductController::class, 'store'])->name('products.store');
            Route::get('/produk/{product}/edit', [Seller\ProductController::class, 'edit'])->name('products.edit');
            Route::put('/produk/{product}', [Seller\ProductController::class, 'update'])->name('products.update');
            Route::patch('/produk/{product}/stok', [Seller\ProductController::class, 'updateStock'])->name('products.stock');
            Route::patch('/produk/{product}/arsip', [Seller\ProductController::class, 'archive'])->name('products.archive');
            Route::delete('/produk/{product}', [Seller\ProductController::class, 'destroy'])->name('products.destroy');

            Route::get('/pesanan', [Seller\OrderController::class, 'index'])->name('orders.index');
            Route::get('/pesanan/catat', [Seller\OrderController::class, 'create'])->name('orders.create');
            Route::post('/pesanan', [Seller\OrderController::class, 'store'])->name('orders.store');
            Route::get('/pesanan/{order}', [Seller\OrderController::class, 'show'])->name('orders.show');
            Route::patch('/pesanan/{order}', [Seller\OrderController::class, 'update'])->name('orders.update');

            Route::get('/laporan', [Seller\SalesReportController::class, 'index'])->name('reports.sales');
            Route::get('/laporan/unduh', [Seller\SalesReportController::class, 'export'])->name('reports.sales.export');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin dashboard (blueprint 2.3)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::get('/verifikasi-penjual', [Admin\SellerVerificationController::class, 'index'])->name('verification.index');
        Route::patch('/verifikasi-penjual/{sellerProfile}/setujui', [Admin\SellerVerificationController::class, 'approve'])->name('verification.approve');
        Route::patch('/verifikasi-penjual/{sellerProfile}/tolak', [Admin\SellerVerificationController::class, 'reject'])->name('verification.reject');

        Route::get('/penjual', [Admin\SellerController::class, 'index'])->name('sellers.index');
        Route::get('/penjual/{seller}', [Admin\SellerController::class, 'show'])->name('sellers.show');
        Route::get('/penjual/{seller}/toko', [Admin\StoreController::class, 'edit'])->name('stores.edit');
        Route::put('/penjual/{seller}/toko', [Admin\StoreController::class, 'update'])->name('stores.update');
        Route::patch('/penjual/{seller}/nonaktifkan', [Admin\SellerController::class, 'suspend'])->name('sellers.suspend');
        Route::patch('/penjual/{seller}/aktifkan', [Admin\SellerController::class, 'reactivate'])->name('sellers.reactivate');

        Route::resource('kategori', Admin\CategoryController::class)
            ->except('show')
            ->parameters(['kategori' => 'category'])
            ->names('categories');

        Route::get('/produk', [Admin\ProductModerationController::class, 'index'])->name('products.index');
        Route::get('/produk/{product}', [Admin\ProductModerationController::class, 'show'])->name('products.show');
        Route::get('/produk/{product}/edit', [Admin\ProductController::class, 'edit'])->name('products.edit');
        Route::put('/produk/{product}', [Admin\ProductController::class, 'update'])->name('products.update');
        Route::patch('/produk/{product}/setujui', [Admin\ProductModerationController::class, 'approve'])->name('products.approve');
        Route::patch('/produk/{product}/tolak', [Admin\ProductModerationController::class, 'reject'])->name('products.reject');
        Route::patch('/produk/{product}/arsip', [Admin\ProductModerationController::class, 'archive'])->name('products.archive');

        Route::get('/pesanan', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');

        Route::resource('banner', Admin\BannerController::class)
            ->except('show')
            ->parameters(['banner' => 'banner'])
            ->names('banners');

        Route::get('/halaman', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::get('/halaman/{page}/edit', [Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('/halaman/{page}', [Admin\PageController::class, 'update'])->name('pages.update');

        Route::get('/laporan', Admin\ReportController::class)->name('reports');
        Route::get('/laporan/penjualan', [Admin\SalesReportController::class, 'index'])->name('reports.sales');
        Route::get('/laporan/penjualan/unduh', [Admin\SalesReportController::class, 'export'])->name('reports.sales.export');

        Route::get('/pengguna', [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/pengguna/tambah-admin', [Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/pengguna', [Admin\UserController::class, 'store'])->name('users.store');
        Route::patch('/pengguna/{user}/status', [Admin\UserController::class, 'updateStatus'])->name('users.status');

        Route::get('/aktivitas', Admin\ActivityLogController::class)->name('activity');

        Route::get('/pengaturan', [Admin\SettingController::class, 'edit'])->name('settings');
        Route::put('/pengaturan', [Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
