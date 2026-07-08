<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\LicenseIssuer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = Order::with(['plan', 'license']);

        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        $orders = $q->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /** Mark an order paid (manual confirmation of an external payment). */
    public function markPaid(Order $order): RedirectResponse
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'paid']);
        }

        return back()->with('status', "Order {$order->code} marked paid.");
    }

    /** Issue the license for a paid (or pending, at your discretion) order. */
    public function issue(Order $order, LicenseIssuer $issuer): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return back()->withErrors(['order' => 'Cannot issue a cancelled order.']);
        }

        $license = $issuer->issueFromOrder($order);

        return redirect()
            ->route('licenses.show', $license)
            ->with('status', "License issued for order {$order->code}: {$license->license_key}");
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->status === 'issued') {
            return back()->withErrors(['order' => 'Order already issued — revoke the license instead.']);
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('status', "Order {$order->code} cancelled.");
    }
}
