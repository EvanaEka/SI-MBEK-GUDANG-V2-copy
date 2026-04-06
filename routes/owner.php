<?php

use App\Http\Controllers\Owner\Auth\OwnerAuthController;
use App\Http\Controllers\Owner\Auth\OwnerForgotPasswordController;
use App\Http\Controllers\Owner\Auth\OwnerResetPasswordController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ProfileController;
use App\Http\Controllers\Owner\KambingController;
use App\Http\Controllers\Owner\DombaController;
use App\Http\Controllers\Owner\PenjualanController;
use App\Http\Controllers\Owner\SupplierController;
use App\Http\Controllers\Owner\PurchaseOrderController;
use App\Http\Controllers\Owner\MaterialInventoryController;
use App\Http\Controllers\Owner\ProductInventoryController;
use App\Http\Controllers\Owner\MaterialController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\FormulaController;
use App\Http\Controllers\Owner\WarehouseDashboardController;
use App\Http\Controllers\Owner\ReportController;
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

        // ============================================
        // PROTECTED ROUTES (dengan must.change.password)
        // ============================================
        Route::middleware('must.change.password')->group(function () {

         //Supplier
            Route::resource('suppliers', SupplierController::class);

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

                    Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
                        ->name('show');

                    Route::patch('/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])
                        ->name('approve');
                });

            //MaterialInventory
            Route::resource(
                'inventory/material',
                MaterialInventoryController::class
            )->only(['index', 'show']);

            Route::post(
                'inventory/material/{material}/adjust',
                [MaterialInventoryController::class, 'adjust']
            )->name('inventory.material.adjust');

            Route::post(
                'inventory/material/{material}/sync',
                [MaterialInventoryController::class, 'sync']
            )->name('inventory.material.sync');

            //MaterialInventory
            Route::resource(
                'inventory/material',
                MaterialInventoryController::class
            )->only(['index', 'show']);

            Route::resource(
                'inventory/product',
                ProductInventoryController::class
            )->only(['index', 'show']);

            //Material Master
Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
Route::get('/materials/create', [MaterialController::class, 'create'])->name('materials.create'); // Tambahkan baris ini
Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
Route::put('/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

//Product Master
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // Tambahkan baris ini
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// Product Inventory (Gunakan ->names() untuk menghindari bentrok dengan Master Data)
Route::resource('inventory/product', ProductInventoryController::class)
    ->names([
        'index' => 'inventory.product.index',
        'show' => 'inventory.product.show',
    ])
    ->only(['index', 'show']);
    
            Route::post(
                'inventory/product/{product}/sync',
                [ProductInventoryController::class, 'sync']
            )->name('inventory.product.sync');

Route::post('inventory/product/{product}/update-rop', [ProductInventoryController::class, 'updateRop'])
    ->name('inventory.product.update-rop');

     //Laporan
            Route::prefix('report')
                ->name('report.')
                ->group(function () {

                    Route::get('/stock', [ReportController::class, 'stock'])
                        ->name('stock');

                    Route::get('/production', [ReportController::class, 'production'])
                        ->name('production');

                    Route::get('/disposal', [ReportController::class, 'disposal'])
                        ->name('disposal');

                    Route::get('/monthly', [ReportController::class, 'monthly'])
                        ->name('monthly');
                });


    //Warehouse
            Route::get('/warehouse', [WarehouseDashboardController::class, 'index'])
    ->name('warehouse.dashboard');

Route::get('/warehouse/activity-log', [WarehouseDashboardController::class, 'activityLog'])
    ->name('warehouse.activity-log');

      // Dashboard
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Profile Management
            Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::post('change-password', [ProfileController::class, 'changePassword'])->name('password.change');

            // Admin Management (Read-Only untuk Owner)
            Route::get('admins', [\App\Http\Controllers\Owner\AdminManagementController::class, 'index'])->name('owners.index');

            // Kambing 
            Route::get('listkambing', [KambingController::class, 'index'])->name('listkambing');
            Route::get('kambing/{kambing}', [KambingController::class, 'show'])->name('kambing.show');
            Route::get('kambing/{id}/monitoring', [KambingController::class, 'monitoring'])->name('kambing.monitoring');

            // Domba 
            Route::get('listdomba', [DombaController::class, 'index'])->name('listdomba');
            Route::get('domba/{domba}', [DombaController::class, 'show'])->name('domba.show');
            Route::get('domba/{id}/monitoring', [DombaController::class, 'monitoring'])->name('domba.monitoring');

            // Penitip 
            Route::get('penitip/{type?}', [ProfileController::class, 'penitip'])
                ->where('type', 'kambing|domba')
                ->name('penitip');

            // Perjanjian & Penjualan
            Route::get('perjanjian', [DashboardController::class, 'perjanjian'])
                ->name('perjanjian');

            // Perjanjian & Penjualan
            Route::get('perjanjian', [DashboardController::class, 'perjanjian'])->name('perjanjian');
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