<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Shop\CustomerAuthController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\PaymentController;
use App\Http\Controllers\Shop\WebhookController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. ADMIN AUTH ROUTES
|--------------------------------------------------------------------------
| Harus sebelum route '/' agar /login tidak tertimpa.
*/
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2. CUSTOMER AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/register',  [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.process');
    Route::get('/login',     [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [CustomerAuthController::class, 'login'])->name('login.process');
    Route::post('/logout',   [CustomerAuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| 3. PUBLIC E-COMMERCE ROUTES (Storefront — tidak perlu login)
|--------------------------------------------------------------------------
*/
Route::name('shop.')->group(function () {
    Route::get('/',             [ShopController::class, 'index'])->name('home');
    Route::get('/catalog',      [ShopController::class, 'catalog'])->name('catalog');
    Route::get('/product/{id}', [ShopController::class, 'show'])->name('product');

    // Sukses checkout — hanya perlu token terenkripsi, tidak perlu login aktif
    Route::get('/order/success/{token}', [CheckoutController::class, 'success'])->name('order.success');
    
    // Invoice PDF & View
    Route::get('/invoice/{token}', [PaymentController::class, 'invoice'])->name('invoice');
    Route::get('/invoice/{token}/pdf', [PaymentController::class, 'downloadPdf'])->name('invoice.pdf');

    // Simulate Payment (Demo Only)
    Route::post('/payment/simulate/{token}', [CheckoutController::class, 'simulatePayment'])->name('order.simulate');
});

/*
|--------------------------------------------------------------------------
| 3b. XENDIT WEBHOOK (Public — tidak perlu login, CSRF dikecualikan)
|--------------------------------------------------------------------------
*/
Route::post('/payment/webhook', [WebhookController::class, 'handle'])->name('payment.webhook');

/*
|--------------------------------------------------------------------------
| 3c. COURIER API (Dummy Endpoint — CSRF dikecualikan)
|--------------------------------------------------------------------------
*/
Route::post('/api/courier/delivered/{orderNumber}', [\App\Http\Controllers\Api\CourierController::class, 'delivered'])
    ->name('api.courier.delivered');

/*
|--------------------------------------------------------------------------
| 4. PROTECTED CUSTOMER ROUTES (Wajib login sebagai customer)
|--------------------------------------------------------------------------
*/
Route::middleware('checkCustomer')->name('shop.')->group(function () {

    // Keranjang belanja
    Route::get('/cart',         [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add',    [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',      [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    // Portal pelanggan (Riwayat, Lacak & Pengaturan)
    Route::get('/my-orders', [\App\Http\Controllers\Shop\CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{orderNumber}', [\App\Http\Controllers\Shop\CustomerOrderController::class, 'show'])->name('orders.show');
    Route::post('/my-orders/review/{orderItem}', [\App\Http\Controllers\Shop\CustomerOrderController::class, 'storeReview'])->name('orders.review');
    
    // Pengaturan Akun
    Route::get('/account/settings', [\App\Http\Controllers\Shop\CustomerSettingsController::class, 'edit'])->name('account.settings');
    Route::put('/account/settings', [\App\Http\Controllers\Shop\CustomerSettingsController::class, 'update'])->name('account.settings.update');
});

/*
|--------------------------------------------------------------------------
| 5. PROTECTED ADMIN / DASHBOARD ROUTES (Wajib login sebagai staf)
|--------------------------------------------------------------------------
*/
Route::middleware(['checkLogin'])->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('checkRole:admin')
        ->name('dashboard.admin');

    Route::get('/dashboard/kasir', [DashboardController::class, 'kasir'])
        ->middleware('checkRole:kasir')
        ->name('dashboard.kasir');

    Route::get('/dashboard/owner', [DashboardController::class, 'owner'])
        ->middleware('checkRole:owner')
        ->name('dashboard.owner');

    // ── Barang (admin only) ───────────────────────────────────────────────
    Route::middleware('checkRole:admin')->group(function () {
        Route::resource('barang', BarangController::class)->except(['show']);
    });

    // Kasir view of gudang (read-only)
    Route::get('/gudang', [BarangController::class, 'kasir'])
        ->middleware('checkRole:kasir')
        ->name('barang.kasir');

    // ── Supplier (admin only) ─────────────────────────────────────────────
    Route::middleware('checkRole:admin')->group(function () {
        Route::resource('supplier', SupplierController::class)->except(['show']);
    });

    // ── User (admin only) ─────────────────────────────────────────────────
    Route::middleware('checkRole:admin')->group(function () {
        Route::resource('user', UserController::class)->except(['show']);
    });

    // ── Pesanan Online E-commerce (admin only) ────────────────────────────
    Route::middleware('checkRole:admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        Route::patch('/orders/{id}/advance', [OrderController::class, 'advanceStatus'])->name('admin.orders.advance');
        Route::patch('/orders/{id}/approve', [OrderController::class, 'approve'])->name('admin.orders.approve');
        Route::post('/orders/{id}/handover', [OrderController::class, 'uploadHandover'])->name('admin.orders.handover');
        Route::post('/orders/{id}/ship', [OrderController::class, 'ship'])->name('admin.orders.ship');
    });

    // ── Barang Masuk (admin & kasir) ──────────────────────────────────────
    Route::middleware('checkRole:admin,kasir')->group(function () {
        Route::resource('barang-masuk', BarangMasukController::class)
            ->except(['show'])
            ->parameters(['barang-masuk' => 'id']);
    });

    // ── Barang Keluar ─────────────────────────────────────────────────────
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])
        ->middleware('checkRole:admin,owner')
        ->name('barang-keluar.index');

    Route::middleware('checkRole:kasir')->group(function () {
        Route::get('/transaksi',        [BarangKeluarController::class, 'kasir'])->name('barang-keluar.kasir');
        Route::get('/transaksi/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create');
        Route::post('/transaksi',       [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
    });

    // ── Laporan & Export ──────────────────────────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/keuangan',      [LaporanController::class, 'keuangan'])->name('keuangan');
        Route::get('/penjualan',     [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('/export-keluar', [LaporanController::class, 'exportExcelKeluar'])->name('export-keluar');
        Route::get('/export-masuk',  [LaporanController::class, 'exportExcelMasuk'])->name('export-masuk');
        Route::get('/cetak-keluar',  [LaporanController::class, 'cetakKeluar'])->name('cetak-keluar');
        Route::get('/cetak-masuk',   [LaporanController::class, 'cetakMasuk'])->name('cetak-masuk');
    });
});
