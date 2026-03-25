<?php

use App\Http\Controllers\KambingForsale;
use App\Http\Controllers\KambingUserController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Admin\Auth\AdminResetPasswordController;
use App\Http\Controllers\Owner\Auth\OwnerForgotPasswordController;
use App\Http\Controllers\Owner\Auth\OwnerResetPasswordController;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/teamproject', function () {
    return view('developer');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::resource('kambings', KambingForsale::class);
Route::get('/forsale', [KambingForsale::class, 'index'])->name('forsale');

// Manual transfer TANPA middleware dulu untuk testing
Route::post('/manual/transfer', [OrderController::class, 'manualTransfer'])->name('manual.transfer');

// Route order untuk user login
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/invoice/{order_id}', [OrderController::class, 'invoice'])->name('order.invoice');

    // PENTING: Route spesifik harus di ATAS sebelum route wildcard
    Route::get('/order/manual-invoice/{order_id}', [OrderController::class, 'manualInvoice'])
        ->name('order.manual-invoice');

    // Route wildcard harus di BAWAH - tambahkan constraint untuk keamanan
    Route::get('/order/{category}/{id}', [OrderController::class, 'show'])
        ->where('category', 'kambing|domba|product')
        ->where('id', '[0-9]+')
        ->name('order.show');

    Route::post('/midtrans/token', [OrderController::class, 'getSnapToken'])->name('midtrans.token');
    Route::get('/transaksi', [OrderController::class, 'transaksi'])->name('order.transaksi');
});

Route::get('/dashboard', [KambingUserController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::post('/midtrans/webhook', [OrderController::class, 'midtransWebhook']);

// ========================================
// ADMIN PASSWORD RESET ROUTES
// ========================================
Route::prefix('admin')->name('admin.')->group(function () {
    // Forgot Password - Form untuk input email
    Route::get('/forgot-password', [AdminForgotPasswordController::class, 'showLinkRequestForm'])
        ->middleware('guest:admin')
        ->name('password.request');

    // Forgot Password - Submit email (kirim link)
    Route::post('/forgot-password', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('guest:admin')
        ->name('password.email');

    // Reset Password - Form untuk input password baru
    Route::get('/reset-password/{token}', [AdminResetPasswordController::class, 'showResetForm'])
        ->middleware('guest:admin')
        ->name('password.reset');

    // Reset Password - Submit password baru
    Route::post('/reset-password', [AdminResetPasswordController::class, 'reset'])
        ->middleware('guest:admin')
        ->name('password.update.admin');
});

// ========================================
// OWNER PASSWORD RESET ROUTES
// ========================================
Route::prefix('owner')->name('owner.')->group(function () {
    // Forgot Password - Form untuk input email
    Route::get('/forgot-password', [OwnerForgotPasswordController::class, 'showLinkRequestForm'])
        ->middleware('guest:owner')
        ->name('password.request');

    // Forgot Password - Submit email (kirim link)
    Route::post('/forgot-password', [OwnerForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('guest:owner')
        ->name('password.email');

    // Reset Password - Form untuk input password baru
    Route::get('/reset-password/{token}', [OwnerResetPasswordController::class, 'showResetForm'])
        ->middleware('guest:owner')
        ->name('password.reset');

    // Reset Password - Submit password baru
    Route::post('/reset-password', [OwnerResetPasswordController::class, 'reset'])
        ->middleware('guest:owner')
        ->name('password.update');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/owner.php';

// CATATAN: Setelah manual transfer berhasil, pindahkan route ke dalam middleware:
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::post('/manual/transfer', [OrderController::class, 'manualTransfer'])->name('manual.transfer');
// });