<?php

use App\Http\Controllers\Api\LicenseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Licensing API (v1)
|--------------------------------------------------------------------------
|
| Called by client deployments to verify / activate / release a license.
| Responses are HMAC-signed (X-License-Signature).
|
*/

Route::prefix('v1/licenses')
    ->middleware('throttle:license-verify')
    ->group(function () {
        Route::post('verify', [LicenseController::class, 'verify']);
        Route::post('deactivate', [LicenseController::class, 'deactivate']);
    });

// Public plans catalog for the main website's pricing section.
Route::get('v1/plans', [\App\Http\Controllers\Api\PlanController::class, 'index'])
    ->middleware('throttle:60,1');
