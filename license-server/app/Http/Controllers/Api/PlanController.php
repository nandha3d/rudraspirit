<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;

/**
 * Public plans catalog — consumed by the main website (animazon.in) to render
 * its pricing section. No auth; read-only; only active plans are exposed.
 */
class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get()
            ->map(fn (Plan $plan) => [
                'name'             => $plan->name,
                'slug'             => $plan->slug,
                'description'      => $plan->description,
                'price'            => (float) $plan->price,
                'currency'         => $plan->currency,
                'billing_period'   => $plan->billing_period,
                'duration_days'    => $plan->duration_days,
                'activation_limit' => $plan->activation_limit,
                'modules'          => $plan->moduleIdentifiers(),
                'features'         => $plan->features ?? [],
                'is_featured'      => $plan->is_featured,
                'checkout_url'     => route('public.checkout', $plan->slug),
            ]);

        return response()->json([
            'data' => $plans,
            'meta' => ['server_time' => now()->toIso8601String(), 'version' => '1.0'],
        ]);
    }
}
