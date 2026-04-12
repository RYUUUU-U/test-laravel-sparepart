<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest only)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['checkLogin'])->group(function () {

    // Redirect root ke login / dashboard
    Route::get('/', function () {
        return redirect()->route('login');
    });

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

    // ── Barang Masuk (admin & kasir) ──────────────────────────────────────
    Route::middleware('checkRole:admin,kasir')->group(function () {
        Route::resource('barang-masuk', BarangMasukController::class)
            ->except(['show'])
            ->parameters(['barang-masuk' => 'id']);
    });

    // ── Barang Keluar ─────────────────────────────────────────────────────
    // Admin & owner: lihat laporan
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])
        ->middleware('checkRole:admin,owner')
        ->name('barang-keluar.index');

    // Kasir: riwayat transaksi + input baru
    Route::middleware('checkRole:kasir')->group(function () {
        Route::get('/transaksi',        [BarangKeluarController::class, 'kasir'])->name('barang-keluar.kasir');
        Route::get('/transaksi/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create');
        Route::post('/transaksi',       [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
    });

    // ── Laporan & Export (admin, kasir, owner semua bisa) ─────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/export-keluar', [LaporanController::class, 'exportExcelKeluar'])->name('export-keluar');
        Route::get('/export-masuk',  [LaporanController::class, 'exportExcelMasuk'])->name('export-masuk');
        Route::get('/cetak-keluar',  [LaporanController::class, 'cetakKeluar'])->name('cetak-keluar');
        Route::get('/cetak-masuk',   [LaporanController::class, 'cetakMasuk'])->name('cetak-masuk');
    });
});
