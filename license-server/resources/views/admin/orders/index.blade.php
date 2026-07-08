@extends('layouts.app')
@section('title', 'Orders')

@section('content')
    <h1>Orders</h1>

    <div class="card">
        <form method="GET" style="display:flex;gap:10px;margin-bottom:16px;">
            <input name="q" value="{{ request('q') }}" placeholder="Search code, name, email, domain…" style="max-width:320px;">
            <select name="status" style="max-width:160px;">
                <option value="">All statuses</option>
                @foreach (['pending','paid','issued','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn secondary">Filter</button>
        </form>

        <table>
            <tr><th>Order</th><th>Plan</th><th>Customer</th><th>Domain</th><th>Amount</th><th>Status</th><th>License</th><th></th></tr>
            @forelse ($orders as $order)
                <tr>
                    <td><code>{{ $order->code }}</code><div class="muted" style="font-size:12px;">{{ $order->created_at->diffForHumans() }}</div></td>
                    <td>{{ $order->plan->name }}</td>
                    <td>{{ $order->customer_name }}<div class="muted" style="font-size:12px;">{{ $order->customer_email }}</div></td>
                    <td class="muted">{{ $order->domain ?: '—' }}</td>
                    <td>{{ $order->currency }} {{ number_format((float) $order->amount, 2) }}</td>
                    <td><span class="pill {{ ['pending'=>'suspended','paid'=>'active','issued'=>'active','cancelled'=>'revoked'][$order->status] ?? '' }}">{{ $order->status }}</span></td>
                    <td>
                        @if ($order->license)
                            <a href="{{ route('licenses.show', $order->license) }}"><code>{{ $order->license->license_key }}</code></a>
                        @else — @endif
                    </td>
                    <td class="row-actions">
                        @if ($order->status === 'pending')
                            <form method="POST" action="{{ route('orders.paid', $order) }}" class="inline">@csrf
                                <button class="btn secondary sm">Mark paid</button>
                            </form>
                        @endif
                        @if (in_array($order->status, ['pending', 'paid']) && ! $order->license_id)
                            <form method="POST" action="{{ route('orders.issue', $order) }}" class="inline"
                                  onsubmit="return confirm('Issue a license for {{ $order->code }}?');">@csrf
                                <button class="btn sm">Issue license</button>
                            </form>
                            <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline">@csrf
                                <button class="btn danger sm">Cancel</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">No orders yet.</td></tr>
            @endforelse
        </table>

        <div style="margin-top:16px;">{{ $orders->links() }}</div>
    </div>
@endsection
