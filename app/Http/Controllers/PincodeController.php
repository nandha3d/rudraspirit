<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\JsonResponse;

class PincodeController extends Controller
{
    public function __construct()
    {
        // Subscription-gated module. When the license plan does not entitle
        // 'indian_pincode', the lookup 404s and the address form silently falls
        // back to manual entry (storefront is never broken).
        $this->middleware(function ($request, $next) {
            abort_unless(feature_allowed('indian_pincode'), 404);
            return $next($request);
        });
    }

    /**
     * Look up an Indian PIN code from the self-hosted pincodes table.
     * Public, read-only, throttled. Used by the address form to auto-fill
     * state / district (city) from the postal code.
     */
    public function lookup(string $pin): JsonResponse
    {
        $pin = preg_replace('/\D/', '', $pin);

        if (strlen($pin) !== 6) {
            return response()->json([
                'status'  => 'invalid',
                'message' => 'PIN code must be 6 digits.',
            ], 422);
        }

        $rows = Pincode::where('pincode', $pin)->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'status'  => 'not_found',
                'pincode' => $pin,
            ], 404);
        }

        $first = $rows->first();

        return response()->json([
            'status'   => 'success',
            'pincode'  => $pin,
            'state'    => $first->state,
            'district' => $first->district,
            'offices'  => $rows->map(fn ($r) => [
                'name'     => $r->office_name,
                'type'     => $r->office_type,
                'district' => $r->district,
            ])->values(),
        ]);
    }
}
