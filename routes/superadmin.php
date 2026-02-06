<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\SuperAdmin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\DombaController;
use App\Http\Controllers\SuperAdmin\GoadController;
use App\Http\Controllers\SuperAdmin\KambingController;
use App\Http\Controllers\SuperAdmin\ProfileController;
use App\Http\Controllers\SuperAdmin\SiteSettingsController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\SuperAdmin\PenjualanController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::prefix('super-admin')->name('super-admin.')->group(function () {

    Route::middleware('guest:super_admin')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    // Auth Routes
    Route::middleware('auth:super_admin')->group(function () {
        Route::get('/domba/{id}/monitoring', [DombaController::class, 'monitoring'])->name('domba.monitoring');
        Route::get('/kambing/{id}/monitoring', [KambingController::class, 'monitoring'])->name('kambing.monitoring');

        //site setting
        Route::get('/site-settings', [SiteSettingsController::class, 'edit'])->name('site-settings.edit');
        Route::put('/site-settings', [SiteSettingsController::class, 'update'])->name('site-settings.update');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');
        Route::delete('/penitip/{user}', [ProfileController::class, 'destroyuser'])->name('profile.destroyuser');

        // Route untuk Tambah Ternak
        Route::get('/tambahkambing', [KambingController::class, 'create'])->name('tambahkambing');
        Route::get('/tambahdomba', [DombaController::class, 'create'])->name('tambahdomba');

        // Route untuk Simpan Ternak
        Route::post('/tambahkambings', [KambingController::class, 'store'])->name('tambahkambing.save');
        Route::post('/tambahdombas', [DombaController::class, 'store'])->name('tambahdomba.save');

        // Route untuk Update & Delete
        Route::put('/tambahkambings/{kambing}', [KambingController::class, 'update'])->name('kambings.update');
        Route::put('/tambahdombas/{domba}', [DombaController::class, 'update'])->name('dombas.update');
        Route::delete('/kambingremove/{kambing}', [KambingController::class, 'destroy'])->name('kambing.destroy');
        Route::delete('/dombaremove/{domba}', [DombaController::class, 'destroy'])->name('domba.destroy');

        // Route untuk Detail
        Route::get('/kambing/{kambing}', [KambingController::class, 'show'])->name('kambing.show');
        Route::get('/domba/{domba}', [DombaController::class, 'show'])->name('domba.show');

        // Route untuk Listing
        Route::get('/listkambing', [KambingController::class, 'index'])->name('listkambing');
        Route::get('/listdomba', [DombaController::class, 'index'])->name('listdomba');

        // Route Terpadu untuk Penitip
        Route::get('/penitip/{type?}', [ProfileController::class, 'penitip'])
            ->where('type', 'kambing|domba')
            ->name('penitip');

        // Order Management Routes
        Route::post('/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);
        Route::post('/orders/{id}/reactivate', [OrderController::class, 'reactivateProduct']);

        // Invoice routes untuk PenjualanController
        Route::get('/penjualan/invoice/{order_id}', [PenjualanController::class, 'invoice'])->name('penjualan.invoice');
        Route::get('/penjualan/manual-invoice/{order_id}', [PenjualanController::class, 'manualInvoice'])->name('penjualan.manual-invoice');

        // Route Lainnya
        Route::get('/perjanjian', [DashboardController::class, 'perjanjian'])->name('perjanjian');
        Route::get('/penjualan', [DashboardController::class, 'penjualan'])->name('penjualan');
        Route::post('/orders/{order}/notes', [DashboardController::class, 'updateNotes'])
            ->name('orders.notes.update');
        Route::post('/orders/{order}/status', [DashboardController::class, 'updateOrderStatus'])->name('orders.status.update');
        // Untuk mengubah status order
        Route::post('/super-admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('super-admin.orders.update-status');

        // Untuk mengaktifkan kembali produk
        Route::post('/super-admin/orders/{order}/reactivate', [OrderController::class, 'reactivateProduct'])->name('super-admin.orders.reactivate');
        // Hapus route duplikat ini:
        // Route::get('penjualan/order/invoice/{order_id}', [PenjualanController::class, 'invoice'])->name('orders.invoice');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});