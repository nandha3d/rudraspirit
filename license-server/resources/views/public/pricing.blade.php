@extends('public._layout')
@section('title', 'Animazon — Plans & Pricing')

@section('content')
    <h1>Choose your plan</h1>
    <p class="sub">Launch your own e-commerce platform. Every plan includes the core engine —<br>pick the modules and scale that fit your business.</p>

    <div class="plans">
        @forelse ($plans as $plan)
            <div class="plan {{ $plan->is_featured ? 'featured' : '' }}">
                @if ($plan->is_featured) <span class="tag">MOST POPULAR</span> @endif
                <h3>{{ $plan->name }}</h3>
                <div class="desc">{{ $plan->description }}</div>
                <div class="price">{{ $plan->currency === 'INR' ? '₹' : $plan->currency . ' ' }}{{ rtrim(rtrim(number_format((float) $plan->price, 2), '0'), '.') }}</div>
                <div class="per">
                    {{ ['monthly' => 'per month', 'yearly' => 'per year', 'lifetime' => 'one-time'][$plan->billing_period] ?? $plan->billing_period }}
                    · {{ $plan->activation_limit }} {{ $plan->activation_limit === 1 ? 'domain' : 'domains' }}
                    · {{ $plan->duration_days ? $plan->duration_days . '-day license' : 'perpetual license' }}
                </div>
                <ul>
                    @foreach ($plan->features ?? [] as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                    @foreach ($plan->moduleIdentifiers() as $module)
                        <li>{{ ucwords(str_replace('_', ' ', $module)) }} module</li>
                    @endforeach
                </ul>
                <a class="btn" href="{{ route('public.checkout', $plan->slug) }}">Choose {{ $plan->name }}</a>
            </div>
        @empty
            <p class="sub">Plans are being prepared — please check back soon.</p>
        @endforelse
    </div>
@endsection
