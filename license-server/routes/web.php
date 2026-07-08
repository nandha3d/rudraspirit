<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LicenseAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\PlanAdminController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

// Landing → pricing (public face of license.animazon.in).
Route::get('/', fn () => redirect()->route('public.pricing'));

// Public pricing + checkout.
Route::get('/pricing', [PublicSiteController::class, 'pricing'])->name('public.pricing');
Route::get('/checkout/{plan:slug}', [PublicSiteController::class, 'checkout'])->name('public.checkout');
Route::post('/checkout/{plan:slug}', [PublicSiteController::class, 'placeOrder'])
    ->middleware('throttle:10,1')->name('public.order');
Route::get('/thanks/{order}', [PublicSiteController::class, 'thanks'])->name('public.thanks');

// Auth.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin panel (session-authenticated).
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('licenses', [LicenseAdminController::class, 'index'])->name('licenses.index');
    Route::get('licenses/create', [LicenseAdminController::class, 'create'])->name('licenses.create');
    Route::post('licenses', [LicenseAdminController::class, 'store'])->name('licenses.store');
    Route::get('licenses/{license}', [LicenseAdminController::class, 'show'])->name('licenses.show');
    Route::get('licenses/{license}/edit', [LicenseAdminController::class, 'edit'])->name('licenses.edit');
    Route::put('licenses/{license}', [LicenseAdminController::class, 'update'])->name('licenses.update');
    Route::delete('licenses/{license}', [LicenseAdminController::class, 'destroy'])->name('licenses.destroy');

    // Entitlements & activations.
    Route::post('licenses/{license}/addons', [LicenseAdminController::class, 'addAddon'])->name('licenses.addons.add');
    Route::delete('licenses/{license}/addons/{addon}', [LicenseAdminController::class, 'removeAddon'])->name('licenses.addons.remove');
    Route::delete('licenses/{license}/activations/{activation}', [LicenseAdminController::class, 'removeActivation'])->name('licenses.activations.remove');

    // Plans (pricing).
    Route::get('plans', [PlanAdminController::class, 'index'])->name('plans.index');
    Route::get('plans/create', [PlanAdminController::class, 'create'])->name('plans.create');
    Route::post('plans', [PlanAdminController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}/edit', [PlanAdminController::class, 'edit'])->name('plans.edit');
    Route::put('plans/{plan}', [PlanAdminController::class, 'update'])->name('plans.update');
    Route::delete('plans/{plan}', [PlanAdminController::class, 'destroy'])->name('plans.destroy');

    // Orders.
    Route::get('orders', [OrderAdminController::class, 'index'])->name('orders.index');
    Route::post('orders/{order}/paid', [OrderAdminController::class, 'markPaid'])->name('orders.paid');
    Route::post('orders/{order}/issue', [OrderAdminController::class, 'issue'])->name('orders.issue');
    Route::post('orders/{order}/cancel', [OrderAdminController::class, 'cancel'])->name('orders.cancel');
});
