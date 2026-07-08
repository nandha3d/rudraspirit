<?php

namespace App\Http\Middleware;

use App\Services\License\LicenseClient;
use Closure;
use Illuminate\Http\Request;

/**
 * Client-side license enforcement for the admin panel.
 *
 * Behaviour by config('license.enforce'):
 *   off / warn / addons : always pass through (a banner may still be shown by
 *                         the view composer; addon installs are gated separately
 *                         in AddonController).
 *   admin               : block the admin panel with a clear notice when the
 *                         deployment is not licensed.
 *
 * This never authenticates anyone and never affects the storefront. It only
 * decides whether to let an already-authenticated admin reach the panel.
 */
class EnsureLicensed
{
    public function __construct(private LicenseClient $license)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->license->enforceMode() !== 'admin') {
            return $next($request);
        }

        $result = $this->license->check();

        if (! ($result['valid'] ?? false)) {
            return response()->view('errors.unlicensed', [
                'status' => $result['status'] ?? 'invalid',
            ], 403);
        }

        return $next($request);
    }
}
