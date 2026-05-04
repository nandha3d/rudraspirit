<?php

/**
 * V3 Storefront API Routes
 *
 * Prefix: /api/v3
 * Middleware: api_v3 (defined in Kernel.php)
 *
 * See docs/HEADLESS_ENGINE_SPEC.md for the full spec and rules.
 */

use App\Http\Controllers\Api\V3\ProductController;
use App\Http\Controllers\Api\V3\CategoryController;
use App\Http\Controllers\Api\V3\BrandController;
use App\Http\Controllers\Api\V3\AuthController;
use App\Http\Controllers\Api\V3\CartController;
use App\Http\Controllers\Api\V3\OrderController;
use App\Http\Controllers\Api\V3\SearchController;
use App\Http\Controllers\Api\V3\ProfileController;
use App\Http\Controllers\Api\V3\AddressController;
use App\Http\Controllers\Api\V3\WishlistController;
use App\Http\Controllers\Api\V3\FlashDealController;
use App\Http\Controllers\Api\V3\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/

Route::get('health', function () {
    return response()->json([
        'success' => true,
        'data'    => [
            'status'  => 'operational',
            'api'     => 'v3',
            'engine'  => config('app.name', 'Headless Commerce') . ' Engine',
        ],
        'meta'    => [
            'timestamp' => now()->toIso8601String(),
            'version'   => config('headless.version', '3.0'),
        ],
        'errors'  => [],
    ]);
})->name('api.v3.health');

/*
|--------------------------------------------------------------------------
| Public Routes — No Authentication Required
|--------------------------------------------------------------------------
*/

// Products
Route::get('products',                       [ProductController::class, 'index'])->name('api.v3.products.index');
Route::get('products/featured',              [ProductController::class, 'featured'])->name('api.v3.products.featured');
Route::get('products/best-sellers',          [ProductController::class, 'bestSellers'])->name('api.v3.products.best_sellers');
Route::get('products/todays-deals',          [ProductController::class, 'todaysDeals'])->name('api.v3.products.todays_deals');
Route::get('products/{slug}',                [ProductController::class, 'show'])->name('api.v3.products.show');
Route::post('products/{slug}/variant-price', [ProductController::class, 'variantPrice'])->name('api.v3.products.variant_price');

// Categories
Route::get('categories',                     [CategoryController::class, 'index'])->name('api.v3.categories.index');
Route::get('categories/featured',            [CategoryController::class, 'featured'])->name('api.v3.categories.featured');
Route::get('categories/{slug}',              [CategoryController::class, 'show'])->name('api.v3.categories.show');

// Brands
Route::get('brands',                         [BrandController::class, 'index'])->name('api.v3.brands.index');
Route::get('brands/{slug}',                  [BrandController::class, 'show'])->name('api.v3.brands.show');

// Flash Deals
Route::get('flash-deals',                    [FlashDealController::class, 'index'])->name('api.v3.flash_deals.index');
Route::get('flash-deals/{slug}',             [FlashDealController::class, 'show'])->name('api.v3.flash_deals.show');

// Search
Route::get('search',                         [SearchController::class, 'search'])->name('api.v3.search');

// Settings / Config
Route::get('settings',                       [SettingsController::class, 'index'])->name('api.v3.settings.index');
Route::get('currencies',                     [SettingsController::class, 'currencies'])->name('api.v3.currencies');
Route::get('languages',                      [SettingsController::class, 'languages'])->name('api.v3.languages');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('register',        [AuthController::class, 'register'])->name('api.v3.auth.register');
    Route::post('login',           [AuthController::class, 'login'])->name('api.v3.auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('logout',    [AuthController::class, 'logout'])->name('api.v3.auth.logout');
        Route::get('user',         [AuthController::class, 'user'])->name('api.v3.auth.user');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Customer Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Cart
    Route::get('cart',                [CartController::class, 'index'])->name('api.v3.cart.index');
    Route::post('cart/items',         [CartController::class, 'addItem'])->name('api.v3.cart.add');
    Route::patch('cart/items/{id}',   [CartController::class, 'updateItem'])->name('api.v3.cart.update');
    Route::delete('cart/items/{id}',  [CartController::class, 'removeItem'])->name('api.v3.cart.remove');

    // Orders
    Route::get('orders',              [OrderController::class, 'index'])->name('api.v3.orders.index');
    Route::get('orders/{code}',       [OrderController::class, 'show'])->name('api.v3.orders.show');
    Route::post('orders/{code}/cancel', [OrderController::class, 'cancel'])->name('api.v3.orders.cancel');

    // Profile
    Route::get('user/profile',        [ProfileController::class, 'show'])->name('api.v3.profile.show');
    Route::patch('user/profile',      [ProfileController::class, 'update'])->name('api.v3.profile.update');

    // Addresses
    Route::get('user/addresses',      [AddressController::class, 'index'])->name('api.v3.addresses.index');
    Route::post('user/addresses',     [AddressController::class, 'store'])->name('api.v3.addresses.store');
    Route::patch('user/addresses/{id}', [AddressController::class, 'update'])->name('api.v3.addresses.update');
    Route::delete('user/addresses/{id}', [AddressController::class, 'destroy'])->name('api.v3.addresses.destroy');

    // Wishlist
    Route::get('user/wishlist',           [WishlistController::class, 'index'])->name('api.v3.wishlist.index');
    Route::post('user/wishlist/{slug}',   [WishlistController::class, 'add'])->name('api.v3.wishlist.add');
    Route::delete('user/wishlist/{slug}', [WishlistController::class, 'remove'])->name('api.v3.wishlist.remove');
});
