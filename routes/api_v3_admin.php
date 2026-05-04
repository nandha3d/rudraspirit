<?php

/**
 * V3 Admin API Routes
 *
 * Prefix: /api/v3/admin
 * Middleware: api_v3, auth:sanctum, admin
 */

use App\Http\Controllers\Api\V3\Admin\ProductController;
use App\Http\Controllers\Api\V3\Admin\OrderController;
use App\Http\Controllers\Api\V3\Admin\CustomerController;
use Illuminate\Support\Facades\Route;

// Note: Ensure the user accessing these routes has the 'admin' middleware passing.

Route::prefix('products')->group(function () {
    Route::get('/',                 [ProductController::class, 'index']);
    Route::post('/',                [ProductController::class, 'store']);
    Route::get('/{id}',             [ProductController::class, 'show']);
    Route::put('/{id}',             [ProductController::class, 'update']);
    Route::delete('/{id}',          [ProductController::class, 'destroy']);
});

Route::prefix('orders')->group(function () {
    Route::get('/',                 [OrderController::class, 'index']);
    Route::get('/{id}',             [OrderController::class, 'show']);
    Route::patch('/{id}/delivery-status', [OrderController::class, 'updateDeliveryStatus']);
    Route::patch('/{id}/payment-status',  [OrderController::class, 'updatePaymentStatus']);
});

Route::prefix('customers')->group(function () {
    Route::get('/',                 [CustomerController::class, 'index']);
    Route::get('/{id}',             [CustomerController::class, 'show']);
    Route::patch('/{id}/ban',       [CustomerController::class, 'updateBan']);
});

Route::prefix('webhooks')->group(function () {
    Route::get('/',                 [\App\Http\Controllers\Api\V3\Admin\WebhookController::class, 'index']);
    Route::post('/',                [\App\Http\Controllers\Api\V3\Admin\WebhookController::class, 'store']);
    Route::put('/{id}',             [\App\Http\Controllers\Api\V3\Admin\WebhookController::class, 'update']);
    Route::delete('/{id}',          [\App\Http\Controllers\Api\V3\Admin\WebhookController::class, 'destroy']);
    Route::get('/{id}/logs',        [\App\Http\Controllers\Api\V3\Admin\WebhookController::class, 'logs']);
});
