<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('pencatatan', InventoryController::class);
Route::post('pencatatan/{item}/mutasi', [InventoryController::class, 'storeMovement'])->name('pencatatan.mutasi');

Route::get('laporan', [ReportController::class, 'index'])->name('laporan.index');
Route::get('laporan/cetak', [ReportController::class, 'print'])->name('laporan.print');

Route::get('notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
Route::post('notifikasi', [NotificationController::class, 'store'])->name('notifikasi.store');
Route::patch('notifikasi/{notificationMessage}/status', [NotificationController::class, 'updateStatus'])->name('notifikasi.status');
