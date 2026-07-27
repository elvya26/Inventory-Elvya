<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\CrmController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Middleware\RequireLogin;

// Shared Google OAuth callback route (used by both Admin and Storefront)
Route::get('/auth/google/callback', [GoogleController::class, 'handleCallback'])->name('login.google.callback');

// =============================================================
// 1. ADMIN PANEL ROUTES (Prefix: /admin)
// =============================================================
Route::prefix('admin')->group(function () {
    Route::get('/login', [GoogleController::class, 'showLogin'])->name('login');
    Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/logout', [GoogleController::class, 'logout'])->name('logout');

    Route::middleware([RequireLogin::class])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('pencatatan', InventoryController::class);
        Route::post('pencatatan/{item}/mutasi', [InventoryController::class, 'storeMovement'])->name('pencatatan.mutasi');

        Route::get('laporan', [ReportController::class, 'index'])->name('laporan.index');
        Route::get('laporan/cetak', [ReportController::class, 'print'])->name('laporan.print');

        Route::get('notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
        Route::post('notifikasi', [NotificationController::class, 'store'])->name('notifikasi.store');
        Route::patch('notifikasi/{notificationMessage}/status', [NotificationController::class, 'updateStatus'])->name('notifikasi.status');

        Route::get('profil', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('profil', [ProfileController::class, 'update'])->name('profile.update');

        // Admin Orders & CRM
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/orders/{order}/ship', [AdminOrderController::class, 'ship'])->name('admin.orders.ship');
        Route::get('/crm', [CrmController::class, 'index'])->name('admin.crm.index');
        Route::post('/crm/{customer}/notes', [CrmController::class, 'updateNotes'])->name('admin.crm.notes');
    });
});

// =============================================================
// 2. CUSTOMER STOREFRONT ROUTES (Prefix: /licitastore)
// =============================================================
Route::prefix('licitastore')->group(function () {
    Route::get('/login', [EcommerceController::class, 'showLogin'])->name('ecommerce.login');
    Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('ecommerce.login.google');
    Route::get('/logout', [GoogleController::class, 'logout'])->name('ecommerce.logout');

    Route::middleware([RequireLogin::class])->group(function () {
        Route::get('/', [EcommerceController::class, 'index'])->name('ecommerce.index');
        Route::get('/product/{item}', [EcommerceController::class, 'show'])->name('ecommerce.show');
        Route::get('/cart', [EcommerceController::class, 'cart'])->name('ecommerce.cart');
        Route::post('/cart/add/{item}', [EcommerceController::class, 'addToCart'])->name('ecommerce.cart.add');
        Route::post('/cart/update', [EcommerceController::class, 'updateCart'])->name('ecommerce.cart.update');

        Route::get('/bank/cek-va', [PaymentController::class, 'showCekVa'])->name('bank.cek_va');
        Route::get('/bank/cek-va/search', [PaymentController::class, 'searchVa'])->name('bank.cek_va.search');
        Route::post('/bank/cek-va/{payment}/pay', [PaymentController::class, 'simulatePay'])->name('bank.cek_va.pay');

        Route::get('/checkout', [EcommerceController::class, 'checkout'])->name('ecommerce.checkout');
        Route::post('/checkout', [EcommerceController::class, 'placeOrder'])->name('ecommerce.checkout.store');
        Route::get('/payment/{order}', [EcommerceController::class, 'payment'])->name('ecommerce.payment');
        Route::get('/payment/{order}/status', [EcommerceController::class, 'paymentStatus'])->name('ecommerce.payment.status');
        Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('ecommerce.payment.notification');
        Route::get('/customer', [EcommerceController::class, 'customerPortal'])->name('ecommerce.customer');
        Route::post('/customer/upload', [EcommerceController::class, 'uploadDoc'])->name('ecommerce.customer.upload');
        Route::get('/track', [EcommerceController::class, 'trackOrder'])->name('ecommerce.track');
    });
});
