@extends('frontend.layouts.app')

@section('content')
@php
    $rsCartTotal = 0;
    foreach ($carts as $cart) {
        $rsCartTotal += cart_product_price($cart, $cart->product, false, false) * $cart->quantity;
    }
@endphp
<main style="max-width:1080px;margin:0 auto;padding:54px 32px 90px;min-height:50vh;">
    <h1 class="rs-serif" style="font-weight:500;font-size:40px;letter-spacing:.04em;text-transform:uppercase;color:var(--rs-ink);margin:0 0 34px;">
        {{ translate('Shopping') }} <em style="color:var(--rs-gold);font-style:italic;">{{ translate('Cart') }}</em>
    </h1>

    @if (count($carts) == 0)
        <div style="text-align:center;padding:70px 0;color:var(--rs-ink-soft);">
            <p style="font-size:19px;margin:0 0 24px;">{{ translate('Your cart is currently empty.') }}</p>
            <a href="{{ route('categories.all') }}" class="rs-btn">{{ translate('Browse Collection') }}</a>
        </div>
    @else
        <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:48px;align-items:start;">
            <div>
                @foreach ($carts as $cart)
                    @php $rsProduct = $cart->product; @endphp
                    @if ($rsProduct)
                    <div style="display:flex;gap:18px;align-items:center;padding:20px 0;border-bottom:1px solid var(--rs-cream-deep);">
                        <a href="{{ route('product', $rsProduct->slug) }}" style="width:74px;height:74px;border-radius:8px;background:linear-gradient(150deg,var(--rs-tan-light),var(--rs-tan));display:flex;align-items:center;justify-content:center;flex:none;overflow:hidden;">
                            @if ($rsProduct->thumbnail)
                                <img src="{{ get_image($rsProduct->thumbnail) }}" alt="{{ $rsProduct->getTranslation('name') }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <span class="rs-bead" style="width:38px;height:38px;"></span>
                            @endif
                        </a>
                        <div style="flex:1;">
                            <div class="rs-serif" style="font-size:20px;color:var(--rs-ink);">{{ $rsProduct->getTranslation('name') }}</div>
                            <div style="font-size:15px;color:var(--rs-ink-muted);margin-top:4px;">{{ cart_product_price($cart, $rsProduct, true, false) }} {{ translate('each') }}</div>
                        </div>
                        <div style="display:flex;align-items:center;border:1px solid var(--rs-tan);border-radius:999px;">
                            <input type="number" value="{{ $cart->quantity }}" min="1" onchange="rsUpdateCartQty({{ $cart->id }}, this.value)" style="width:46px;text-align:center;border:none;outline:none;padding:8px 0;">
                        </div>
                        <div style="min-width:90px;text-align:right;font-size:18px;color:var(--rs-gold-deep);">
                            {{ single_price(cart_product_price($cart, $rsProduct, false, false) * $cart->quantity) }}
                        </div>
                        <button type="button" onclick="rsRemoveCartItem({{ $cart->id }})" style="border:none;background:none;cursor:pointer;color:var(--rs-ink-faint);font-size:21px;">&times;</button>
                    </div>
                    @endif
                @endforeach
            </div>
            <div style="background:var(--rs-cream);border-radius:10px;padding:30px;">
                <h3 class="rs-serif" style="font-size:24px;font-weight:500;color:var(--rs-ink);margin:0 0 22px;">{{ translate('Order Summary') }}</h3>
                <div style="display:flex;justify-content:space-between;font-size:17px;color:var(--rs-ink-soft);padding:10px 0;border-bottom:1px solid var(--rs-cream-deep);">
                    <span>{{ translate('Subtotal') }}</span><span>{{ single_price($rsCartTotal) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:21px;color:var(--rs-ink);padding:16px 0 22px;font-family:'Playfair Display',serif;">
                    <span>{{ translate('Total') }}</span><span>{{ single_price($rsCartTotal) }}</span>
                </div>
                <a href="{{ route('checkout') }}" class="rs-btn" style="display:block;text-align:center;">{{ translate('Proceed to Checkout') }}</a>
            </div>
        </div>
    @endif
</main>
@endsection
