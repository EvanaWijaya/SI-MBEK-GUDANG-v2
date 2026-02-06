<?php

use App\Http\Controllers\Owner\Auth\OwnerAuthController;
use App\Http\Controllers\Owner\Auth\OwnerForgotPasswordController;
use App\Http\Controllers\Owner\Auth\OwnerResetPasswordController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ProfileController;
use App\Http\Controllers\Admin\KambingController;
use App\Http\Controllers\Admin\DombaController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\WarehouseDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('owner')->name('owner.')->group(function () {

    // ============================================
    // GUEST ROUTES (Belum Login)
    // ============================================
    Route::middleware('guest:owner')->group(function () {
        // Login
        Route::get('login', [OwnerAuthController::class, 'create'])->name('login');
        Route::post('login', [OwnerAuthController::class, 'store']);

        // Forgot Password
        Route::get('forgot-password', [OwnerForgotPasswordController::class, 'showLinkRequestForm'])
            ->name('password.request');
        Route::post('forgot-password', [OwnerForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('password.email');

        // Reset Password
        Route::get('reset-password/{token}', [OwnerResetPasswordController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('reset-password', [OwnerResetPasswordController::class, 'reset'])
            ->name('password.update');
    });

    // ============================================
    // AUTHENTICATED ROUTES
    // ============================================
    Route::middleware('auth:owner')->group(function () {

        // Logout
        Route::post('logout', [OwnerAuthController::class, 'destroy'])->name('logout');

        // Change Password (tanpa middleware must.change.password)
        Route::get('change-password', [ProfileController::class, 'showChangePasswordForm'])
            ->name('password.change.form');
        Route::post('change-password', [ProfileController::class, 'changePassword'])
            ->name('password.change');

        // ============================================
        // PROTECTED ROUTES (dengan must.change.password)
        // ============================================
        Route::middleware('must.change.password')->group(function () {

            // Order Bahan Baku
            Route::prefix('purchase-orders')
                ->name('purchase-orders.')
                ->group(function () {

                    // Tambahkan route yang kurang ini:
                    Route::get('/', [PurchaseOrderController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [PurchaseOrderController::class, 'create'])
                        ->name('create');

                    Route::post('/', [PurchaseOrderController::class, 'store'])
                        ->name('store');

                    Route::put('/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])
                        ->name('approve');
                });
            
            //Warehouse
            Route::get('/warehouse',[WarehouseDashboardController::class, 'index'])->name('warehouse.dashboard');

            // Dashboard (Read-only)
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Profile (Read-only, hanya bisa update password)
            Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');

            // Kambing (Read-only)
            Route::get('kambing', [KambingController::class, 'index'])->name('kambing.index');
            Route::get('kambing/{kambing}', [KambingController::class, 'show'])->name('kambing.show');
            Route::get('kambing/{id}/monitoring', [KambingController::class, 'monitoring'])->name('kambing.monitoring');

            // Domba (Read-only)
            Route::get('domba', [DombaController::class, 'index'])->name('domba.index');
            Route::get('domba/{domba}', [DombaController::class, 'show'])->name('domba.show');
            Route::get('domba/{id}/monitoring', [DombaController::class, 'monitoring'])->name('domba.monitoring');

            // Penjualan (Read-only)
            Route::get('penjualan', [DashboardController::class, 'penjualan'])->name('penjualan');
            Route::get('penjualan/invoice/{order_id}', [PenjualanController::class, 'invoice'])->name('penjualan.invoice');
            Route::get('penjualan/manual-invoice/{order_id}', [PenjualanController::class, 'manualInvoice'])->name('penjualan.manual-invoice');

            // Reports (Read-only)
            Route::get('reports/kambing', [DashboardController::class, 'kambingReport'])->name('reports.kambing');
            Route::get('reports/domba', [DashboardController::class, 'dombaReport'])->name('reports.domba');
            Route::get('reports/penjualan', [DashboardController::class, 'penjualanReport'])->name('reports.penjualan');
        });
    });
});