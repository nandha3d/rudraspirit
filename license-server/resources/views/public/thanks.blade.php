@extends('public._layout')
@section('title', 'Order received')

@section('content')
    <div style="max-width:560px;margin:0 auto;text-align:center;">
        <h1>Thank you, {{ $order->customer_name }}!</h1>
        <div class="card" style="margin-top:24px;">
            <p>Your order for the <strong>{{ $order->plan->name }}</strong> plan has been received.</p>
            <p>Order reference: <code>{{ $order->code }}</code></p>
            <p style="color:var(--mut);">
                We will confirm your payment and email your license key to
                <strong>{{ $order->customer_email }}</strong> shortly.
            </p>
        </div>
        <p style="margin-top:16px;"><a href="{{ route('public.pricing') }}">← Back to plans</a></p>
    </div>
@endsection
