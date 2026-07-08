@extends('layouts.app')
@section('title', 'Plans')

@section('content')
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
        <h1 style="margin:0;">Plans</h1>
        <span style="flex:1;"></span>
        <a class="btn" href="{{ route('plans.create') }}">+ New plan</a>
    </div>

    <div class="card">
        <table>
            <tr><th>Plan</th><th>Price</th><th>Validity</th><th>Domains</th><th>Modules</th><th>Licenses</th><th>Orders</th><th>Status</th><th></th></tr>
            @forelse ($plans as $plan)
                <tr>
                    <td>
                        <strong>{{ $plan->name }}</strong>
                        @if ($plan->is_featured) <span class="pill active">featured</span> @endif
                        <div class="muted" style="font-size:12px;">{{ $plan->slug }}</div>
                    </td>
                    <td>{{ $plan->currency }} {{ number_format((float) $plan->price, 2) }} <span class="muted">/ {{ $plan->billing_period }}</span></td>
                    <td class="muted">{{ $plan->duration_days ? $plan->duration_days . ' days' : 'perpetual' }}</td>
                    <td>{{ $plan->activation_limit }}</td>
                    <td class="muted">{{ count($plan->moduleIdentifiers()) }}</td>
                    <td>{{ $plan->licenses_count }}</td>
                    <td>{{ $plan->orders_count }}</td>
                    <td><span class="pill {{ $plan->is_active ? 'active' : 'revoked' }}">{{ $plan->is_active ? 'active' : 'inactive' }}</span></td>
                    <td class="row-actions">
                        <a class="btn secondary sm" href="{{ route('plans.edit', $plan) }}">Edit</a>
                        <form method="POST" action="{{ route('plans.destroy', $plan) }}" class="inline"
                              onsubmit="return confirm('Delete plan {{ $plan->name }}?');">
                            @csrf @method('DELETE')
                            <button class="btn danger sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="muted">No plans yet — create one to power the pricing page.</td></tr>
            @endforelse
        </table>
    </div>
@endsection
