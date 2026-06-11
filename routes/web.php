<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Middleware\RequireLogin;

Route::get('/login', [GoogleController::class, 'showLogin'])->name('login');
Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleCallback'])->name('login.google.callback');
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
});

