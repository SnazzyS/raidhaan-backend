<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RestaurantSettingController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserManagementController;
use App\Models\Category;
use App\Models\Item;
use App\Models\RestaurantSetting;
use App\Models\Sale;
use App\Models\User;
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
    Route::get('categories', [CategoryController::class, 'webIndex'])->can('viewAny', Category::class)->name('categories.index');
    Route::post('categories', [CategoryController::class, 'webStore'])->can('create', Category::class)->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'webUpdate'])->can('update', 'category')->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'webDestroy'])->can('delete', 'category')->name('categories.destroy');

    // Items
    Route::get('items', [ItemController::class, 'webIndex'])->can('viewAny', Item::class)->name('items.index');
    Route::post('items', [ItemController::class, 'webStore'])->can('create', Item::class)->name('items.store');
    Route::put('items/{item}', [ItemController::class, 'webUpdate'])->can('update', 'item')->name('items.update');
    Route::delete('items/{item}', [ItemController::class, 'webDestroy'])->can('delete', 'item')->name('items.destroy');

    // Sales
    Route::get('sales', [SaleController::class, 'webIndex'])->can('viewAny', Sale::class)->name('sales.index');

    // Settings
    Route::get('settings', [RestaurantSettingController::class, 'index'])->can('manage', RestaurantSetting::class)->name('settings.index');
    Route::put('settings', [RestaurantSettingController::class, 'update'])->can('manage', RestaurantSetting::class)->name('settings.update');

    // Users
    Route::get('users', [UserManagementController::class, 'index'])->can('viewAny', User::class)->name('users.index');
    Route::post('users', [UserManagementController::class, 'store'])->can('create', User::class)->name('users.store');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->can('update', 'user')->name('users.update');
    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->can('delete', 'user')->name('users.destroy');
});
