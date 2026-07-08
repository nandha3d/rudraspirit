<?php

namespace App\Services;

use App\Models\License;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Issues licenses from plans/orders. The plan defines the entitlements
 * (modules, activation limit, validity); the license records the customer.
 */
class LicenseIssuer
{
    /**
     * Issue a license for a paid/approved order and mark the order issued.
     */
    public function issueFromOrder(Order $order): License
    {
        return DB::transaction(function () use ($order) {
            if ($order->license_id) {
                return $order->license; // idempotent — never double-issue
            }

            $plan = $order->plan;

            $license = License::create([
                'license_key'      => License::generateKey(),
                'product'          => config('license.default_product'),
                'plan_id'          => $plan->id,
                'customer_name'    => $order->customer_name,
                'customer_email'   => $order->customer_email,
                'status'           => 'active',
                'activation_limit' => $plan->activation_limit,
                'expires_at'       => $plan->duration_days
                    ? Carbon::now()->addDays($plan->duration_days)
                    : null,
                'notes'            => "Issued from order {$order->code} (plan: {$plan->name}).",
            ]);

            $order->forceFill([
                'license_id' => $license->id,
                'status'     => 'issued',
            ])->save();

            return $license;
        });
    }
}
