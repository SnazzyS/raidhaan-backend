<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\QzTrayController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('orders', [OrderController::class, 'webIndex'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'webStore'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'webShow'])->name('orders.show');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'webUpdate'])->name('orders.update');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // Categories
    Route::get('categories', [CategoryController::class, 'webIndex'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'webStore'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'webUpdate'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'webDestroy'])->name('categories.destroy');

    // Items
    Route::get('items', [ItemController::class, 'webIndex'])->name('items.index');
    Route::post('items', [ItemController::class, 'webStore'])->name('items.store');
    Route::put('items/{item}', [ItemController::class, 'webUpdate'])->name('items.update');
    Route::delete('items/{item}', [ItemController::class, 'webDestroy'])->name('items.destroy');

    // Sales
    Route::get('sales', [SaleController::class, 'webIndex'])->name('sales.index');

    // QZ Tray
    Route::get('qz/certificate', [QzTrayController::class, 'certificate'])->name('qz.certificate');
    Route::post('qz/sign', [QzTrayController::class, 'sign'])->name('qz.sign');
});
