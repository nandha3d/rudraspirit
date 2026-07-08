<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LicenseAdminController;
use Illuminate\Support\Facades\Route;

// Landing → admin.
Route::get('/', fn () => redirect('/admin'));

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
});
