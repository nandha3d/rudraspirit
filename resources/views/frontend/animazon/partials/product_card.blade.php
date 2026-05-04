<div class="product-card">
    <a href="{{ route('product', $product->slug) }}" class="product-image-wrap">
        <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ $product->getTranslation('name') }}">
        @if($product->discount > 0)
            <div class="product-badge">
                @if($product->discount_type == 'percent')
                    -{{ $product->discount }}%
                @else
                    -{{ single_price($product->discount) }}
                @endif
            </div>
        @endif
    </a>
    
    <div class="product-info">
        @if($product->brand)
            <div class="product-brand">{{ $product->brand->getTranslation('name') }}</div>
        @endif
        
        <a href="{{ route('product', $product->slug) }}" class="product-name" title="{{ $product->getTranslation('name') }}">
            {{ $product->getTranslation('name') }}
        </a>
        
        <div class="product-price-row">
            <div>
                <span class="product-price">{{ home_discounted_base_price($product) }}</span>
                @if(home_base_price($product) != home_discounted_base_price($product))
                    <span class="product-price-old">{{ home_base_price($product) }}</span>
                @endif
            </div>
            
            <button class="btn-icon" title="{{ translate('Add to Cart') }}" onclick="animazonApi.addToCart({{ $product->id }})">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </button>
        </div>
    </div>
</div>
