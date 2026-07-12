@php
    $rsProductUrl = route('product', $product->slug);
    $rsThumb = $product->thumbnail ?? null;
@endphp
<div class="rs-card">
    <div class="rs-card-media-wrap">
        <a href="{{ $rsProductUrl }}" class="rs-card-media">
            @if ($rsThumb)
                <img src="{{ get_image($product->thumbnail) }}" alt="{{ $product->getTranslation('name') }}">
            @else
                <img src="{{ static_asset('assets/img/pages/rudraspirit/Gemini_Generated_Image_2ht5mi2ht5mi2ht5.webp') }}" alt="{{ $product->getTranslation('name') }}">
            @endif
        </a>
        @if (discount_in_percentage($product) > 0)
            <span class="rs-card-badge rs-card-badge-sale">-{{ discount_in_percentage($product) }}%</span>
        @endif
        <div class="rs-card-quick">
            @if (feature_allowed('wishlist'))
            <button type="button" class="rs-card-quick-btn" title="{{ translate('Add to Wishlist') }}"
                @if (Auth::check())
                    onclick="addToWishList({{ $product->id }})"
                @else
                    onclick="showLoginModal()"
                @endif
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            </button>
            @endif
            @if ($product->variant_product)
                <button type="button" class="rs-card-quick-btn" title="{{ translate('Quick View') }}" onclick="showAddToCartRightCanvas({{ $product->id }})">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
            @endif
        </div>
    </div>
    <a href="{{ $rsProductUrl }}" class="rs-card-title">{{ $product->getTranslation('name') }}</a>
    @if ($product->tags)
        <div class="rs-card-deity">{{ translate('Ruling Deity') }}: {{ explode(',', $product->tags)[0] }}</div>
    @endif
    <div class="rs-card-price">
        @if (home_base_price($product) != home_discounted_base_price($product))
            <del>{{ home_base_price($product) }}</del>
        @endif
        {{ home_discounted_base_price($product) }}
    </div>
    @if ($product->current_stock > 0)
        <div class="rs-card-stock-status">
            <span class="rs-stock-dot"></span>
            <span>{{ translate('In Stock') }}</span>
        </div>
    @endif
    <div class="rs-card-actions">
        @if ($product->variant_product)
            <button type="button" class="rs-card-btn-cart" onclick="showAddToCartRightCanvas({{ $product->id }})">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                {{ translate('Add to Cart') }}
            </button>
        @else
            <button type="button" class="rs-card-btn-cart"
                @if (Auth::check() || get_setting('guest_checkout_activation') == 1)
                    onclick="addToCartSingleProduct({{ $product->id }})"
                @else
                    onclick="showLoginModal()"
                @endif
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                {{ translate('Add to Cart') }}
            </button>
        @endif
        <a href="{{ $rsProductUrl }}" class="rs-card-btn-buy">{{ translate('Buy Now') }}</a>
    </div>
</div>
