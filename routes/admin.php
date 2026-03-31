<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Admin\Auth\AdminResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DombaController;
use App\Http\Controllers\Admin\KambingController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\MaterialInventoryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\Admin\FormulaController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\ProductInventoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductAllocationController;
use App\Http\Controllers\Admin\ProductionQcController;
use App\Http\Controllers\Admin\DisposalController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    // ============================================
    // GUEST ROUTES (Belum Login)
    // ============================================
    Route::middleware('guest:admin')->group(function () {
        // Login
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        // Forgot Password
        Route::get('forgot-password', [AdminForgotPasswordController::class, 'showLinkRequestForm'])
            ->name('password.request');
        Route::post('forgot-password', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('password.email');

        // Reset Password
        Route::get('reset-password/{token}', [AdminResetPasswordController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('reset-password', [AdminResetPasswordController::class, 'reset'])
            ->name('password.update');
    });

    // ============================================
    // AUTHENTICATED ROUTES
    // ============================================
    Route::middleware(['auth:admin'])->group(function () {

        // Logout
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

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

                    // PurchaseOrder:
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

                    Route::post('/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])
                        ->name('receive');
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

             Route::get('/productions', [ProductionController::class, 'index'])->name('productions.index');
Route::get('/productions/create', [ProductionController::class, 'create'])->name('productions.create');
Route::get('/productions/{production}', [ProductionController::class, 'show'])->name('productions.show');

            //Formula
            Route::resource('formula', FormulaController::class)
                ->except(['show']);

            //Production
            Route::post('/productions', [ProductionController::class, 'store'])
                ->name('productions.store');

            Route::put(
                '/productions/{production}/qc',
                [ProductionController::class, 'qc']
            )->name('productions.qc');

            Route::put(
                '/productions/{production}/selesai',
                [ProductionController::class, 'selesai']
            )->name('productions.selesai');

            Route::post(
                '/products/{product}/allocations/internal',
                [ProductAllocationController::class, 'useInternal']
            )->name('product.allocations.use-internal');

            Route::post(
                '/products/{product}/allocations/sell',
                [ProductAllocationController::class, 'sell']
            )->name('product.allocations.sell');

            Route::post(
                '/productions/{production}/qc',
                [ProductionQcController::class, 'store']
            )->name('qc.store');

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

            Route::post(
                'inventory/product/{product}/adjust',
                [ProductInventoryController::class, 'adjust']
            )->name('inventory.product.adjust');

            Route::post(
    '/products/{product}/allocations/set',
    [ProductAllocationController::class, 'storeOrUpdate']
)->name('product.allocations.set');

Route::post('inventory/product/{product}/update-rop', [ProductInventoryController::class, 'updateRop'])
    ->name('inventory.product.update-rop');

            //Disposal
            Route::post('/disposal/material/{stock}', [DisposalController::class, 'disposeMaterial']);
            Route::post('/disposal/production/{production}', [DisposalController::class, 'disposeProduction']);
            Route::post('/disposal/product-batch/{stock}', [DisposalController::class, 'disposeProductBatch']);

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

            // ============================================
            // SUPER ADMIN ONLY ROUTES
            // ============================================
            Route::middleware('role:super_admin')->group(function () {

                // Admin Management
                Route::resource('admins', AdminManagementController::class);

                // Site Settings
                Route::get('site-settings', [SiteSettingsController::class, 'edit'])->name('site-settings.edit');
                Route::put('site-settings', [SiteSettingsController::class, 'update'])->name('site-settings.update');

                // Delete Penitip User
                Route::delete('penitip/{user}', [ProfileController::class, 'destroyuser'])->name('profile.destroyuser');
            });

            // ============================================
            // ALL ADMIN ROUTES (Super Admin + Admin Biasa)
            // ============================================

            // Kambing Management
            Route::get('tambahkambing', [KambingController::class, 'create'])->name('tambahkambing');
            Route::post('tambahkambings', [KambingController::class, 'store'])->name('tambahkambing.save');
            Route::get('listkambing', [KambingController::class, 'index'])->name('listkambing');
            Route::get('kambing/{kambing}', [KambingController::class, 'show'])->name('kambing.show');
            Route::get('kambing/{id}/monitoring', [KambingController::class, 'monitoring'])->name('kambing.monitoring');
            Route::put('tambahkambings/{kambing}', [KambingController::class, 'update'])->name('kambings.update');
            Route::delete('kambingremove/{kambing}', [KambingController::class, 'destroy'])->name('kambing.destroy');
            Route::post('kambing/{kambing}/history', [KambingController::class, 'storeHistory'])->name('kambing.history.store');

            // Domba Management
            Route::get('tambahdomba', [DombaController::class, 'create'])->name('tambahdomba');
            Route::post('tambahdombas', [DombaController::class, 'store'])->name('tambahdomba.save');
            Route::get('listdomba', [DombaController::class, 'index'])->name('listdomba');
            Route::get('domba/{domba}', [DombaController::class, 'show'])->name('domba.show');
            Route::get('domba/{id}/monitoring', [DombaController::class, 'monitoring'])->name('domba.monitoring');
            Route::put('tambahdombas/{domba}', [DombaController::class, 'update'])->name('dombas.update');
            Route::delete('dombaremove/{domba}', [DombaController::class, 'destroy'])->name('domba.destroy');
            Route::post('domba/{domba}/history', [DombaController::class, 'storeHistory'])->name('domba.history.store');

            // Penitip Routes
            Route::get('penitip/{type?}', [ProfileController::class, 'penitip'])
                ->where('type', 'kambing|domba')
                ->name('penitip');

            // Perjanjian & Penjualan
            Route::get('perjanjian', [DashboardController::class, 'perjanjian'])->name('perjanjian');
            Route::get('penjualan', [DashboardController::class, 'penjualan'])->name('penjualan');
            Route::get('penjualan/invoice/{order_id}', [PenjualanController::class, 'invoice'])->name('penjualan.invoice');
            Route::get('penjualan/manual-invoice/{order_id}', [PenjualanController::class, 'manualInvoice'])->name('penjualan.manual-invoice');
            

            // Order Management
            Route::post('orders/{order}/notes', [DashboardController::class, 'updateNotes'])->name('orders.notes.update');
            Route::post('orders/{order}/status', [DashboardController::class, 'updateStatus'])->name('orders.status.update');
            Route::post('orders/{id}/update-status', [OrderController::class, 'updateOrderStatus'])->name('orders.update-status');
            Route::post('orders/{id}/reactivate', [OrderController::class, 'reactivateProduct'])->name('orders.reactivate');
        });
    });
});