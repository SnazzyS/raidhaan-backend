<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Models\Category;
use App\Models\Item;
use App\Models\Sale;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('categories', [CategoryController::class, 'index'])->can('viewAny', Category::class);
    Route::post('categories', [CategoryController::class, 'store'])->can('create', Category::class);
    Route::put('categories/{category}', [CategoryController::class, 'update'])->can('update', 'category');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->can('delete', 'category');

    Route::post('items', [ItemController::class, 'store'])->can('create', Item::class);
    Route::put('items/{item}', [ItemController::class, 'update'])->can('update', 'item');
    Route::delete('items/{item}', [ItemController::class, 'destroy'])->can('delete', 'item');

    Route::get('orders/cancelled', [OrderController::class, 'cancelledOrders']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::put('orders/{order}', [OrderController::class, 'update']);

    Route::get('sales', [SaleController::class, 'index'])->can('viewAny', Sale::class);
});

Route::get('orders/{order}/receipt', [OrderController::class, 'generateReceipt']);
