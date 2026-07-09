<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductAuditLogController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show'])->whereNumber('id');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        Route::middleware('role:admin')->group(function () {
            Route::get('admin/products', [ProductController::class, 'adminIndex']);
            Route::post('products', [ProductController::class, 'store']);
            Route::put('products/{product}', [ProductController::class, 'update']);
            Route::delete('products/{product}', [ProductController::class, 'destroy']);
            Route::get('product-audit-logs', [ProductAuditLogController::class, 'index']);
            Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus'])->whereNumber('id');
        });

        Route::middleware('role:customer')->group(function () {
            Route::get('cart', [CartController::class, 'show']);
            Route::post('cart/items', [CartController::class, 'addItem']);
            Route::put('cart/items/{itemId}', [CartController::class, 'updateItem'])->whereNumber('itemId');
            Route::delete('cart/items/{itemId}', [CartController::class, 'removeItem'])->whereNumber('itemId');
            Route::post('orders', [OrderController::class, 'store']);
        });

        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show'])->whereNumber('id');
    });
});
