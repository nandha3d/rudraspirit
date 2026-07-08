<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public pricing + checkout flow.
 *
 * Customers pick a plan on /pricing (or on the main website via the JSON
 * plans API), fill the checkout form, and an order is created. If the plan
 * has an external payment link they are redirected there; otherwise they see
 * a confirmation and you complete payment out-of-band. Admin then marks the
 * order paid and issues the license (one click).
 */
class PublicSiteController extends Controller
{
    public function pricing(): View
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('public.pricing', compact('plans'));
    }

    public function checkout(Plan $plan): View
    {
        abort_unless($plan->is_active, 404);

        return view('public.checkout', compact('plan'));
    }

    public function placeOrder(Request $request, Plan $plan): RedirectResponse
    {
        abort_unless($plan->is_active, 404);

        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'domain'         => ['nullable', 'string', 'max:255'],
        ]);

        $order = Order::create([
            'code'           => Order::generateCode(),
            'plan_id'        => $plan->id,
            'customer_name'  => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'domain'         => $data['domain'] ?? null,
            'amount'         => $plan->price,
            'currency'       => $plan->currency,
            'status'         => 'pending',
        ]);

        // External payment page (e.g. a Razorpay/Stripe payment link) if set.
        if ($plan->payment_link) {
            return redirect()->away($plan->payment_link)
                ->with('order_code', $order->code);
        }

        return redirect()->route('public.thanks', ['order' => $order->code]);
    }

    public function thanks(string $order): View
    {
        $order = Order::with('plan')->where('code', $order)->firstOrFail();

        return view('public.thanks', compact('order'));
    }
}
