@extends('public._layout')
@section('title', 'Checkout — ' . $plan->name)

@section('content')
    <div style="max-width:520px;margin:0 auto;">
        <h1>Checkout</h1>
        <p class="sub">
            <strong>{{ $plan->name }}</strong> —
            {{ $plan->currency === 'INR' ? '₹' : $plan->currency . ' ' }}{{ rtrim(rtrim(number_format((float) $plan->price, 2), '0'), '.') }}
            {{ ['monthly' => 'per month', 'yearly' => 'per year', 'lifetime' => 'one-time'][$plan->billing_period] ?? '' }}
        </p>

        <div class="card">
            <form method="POST" action="{{ route('public.order', $plan->slug) }}">
                @csrf
                <div class="field">
                    <label>Your name</label>
                    <input name="customer_name" value="{{ old('customer_name') }}" required>
                </div>
                <div class="field">
                    <label>Email (your license key will be sent here)</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" required>
                </div>
                <div class="field">
                    <label>Store domain (optional — e.g. mystore.com)</label>
                    <input name="domain" value="{{ old('domain') }}" placeholder="mystore.com">
                </div>
                <button class="btn">{{ $plan->payment_link ? 'Continue to payment' : 'Place order' }}</button>
            </form>
        </div>
        <p style="text-align:center;margin-top:16px;"><a href="{{ route('public.pricing') }}">← Back to plans</a></p>
    </div>
@endsection
