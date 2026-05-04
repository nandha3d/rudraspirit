@extends('frontend.animazon.layouts.app')

@section('meta_title'){{ $detailedProduct->meta_title }}@stop
@section('meta_description'){{ $detailedProduct->meta_description }}@stop
@section('meta_keywords'){{ $detailedProduct->meta_keywords }}@stop

@section('content')

<div class="animazon-container">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; margin-bottom: 4rem;">
        
        <!-- Product Image Gallery -->
        <div class="product-gallery">
            <div class="main-image" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; margin-bottom: 1rem;">
                <img src="{{ uploaded_asset($detailedProduct->thumbnail_img) }}" alt="{{ $detailedProduct->getTranslation('name') }}" style="width: 100%; height: auto; object-fit: contain; max-height: 500px;" id="main-product-image">
            </div>
            @if($detailedProduct->photos != null)
            <div class="thumbnails" style="display: flex; gap: 1rem; overflow-x: auto;">
                @foreach (explode(',', $detailedProduct->photos) as $key => $photo)
                    <div class="thumbnail" style="width: 80px; height: 80px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.5rem; cursor: pointer;" onclick="document.getElementById('main-product-image').src='{{ uploaded_asset($photo) }}'">
                        <img src="{{ uploaded_asset($photo) }}" alt="" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="product-info-panel">
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">{{ $detailedProduct->getTranslation('name') }}</h1>
            
            @if($detailedProduct->brand)
                <div style="color: var(--text-secondary); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1px; font-size: 0.875rem;">
                    {{ $detailedProduct->brand->getTranslation('name') }}
                </div>
            @endif

            <form id="add-to-cart-form">
                <div class="price-section" style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color);">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--accent-brand); font-family: 'Outfit', sans-serif;">
                        {{ home_discounted_base_price($detailedProduct) }}
                    </div>
                    @if(home_base_price($detailedProduct) != home_discounted_base_price($detailedProduct))
                        <div style="text-decoration: line-through; color: var(--text-secondary);">
                            {{ home_base_price($detailedProduct) }}
                        </div>
                    @endif
                </div>

                <!-- Variant Selection -->
                @if ($detailedProduct->colors != null && count(json_decode($detailedProduct->colors)) > 0)
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">{{ translate('Color') }}</h4>
                        <div style="display: flex; gap: 0.75rem;">
                            @foreach (json_decode($detailedProduct->colors) as $key => $color)
                                <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-surface);">
                                    <input type="radio" name="color" value="{{ get_single_color_name($color) }}" @if ($key == 0) checked @endif style="accent-color: var(--accent-primary);">
                                    <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background-color: {{ $color }}; border: 1px solid #fff;"></span>
                                    <span>{{ get_single_color_name($color) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($detailedProduct->choice_options != null)
                    @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">{{ get_single_attribute_name($choice->attribute_id) }}</h4>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                                @foreach ($choice->values as $v_key => $value)
                                    <label style="cursor: pointer; padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-surface);">
                                        <input type="radio" name="attribute_id_{{ $choice->attribute_id }}" value="{{ $value }}" @if ($v_key == 0) checked @endif style="accent-color: var(--accent-primary);">
                                        <span style="margin-left: 0.25rem;">{{ $value }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Add to Cart Action -->
                <div class="actions" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 3rem;">
                    <div style="display: flex; border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden;">
                        <button type="button" onclick="let q=document.getElementById('qty'); q.value=Math.max(1, parseInt(q.value)-1);" style="background: var(--bg-surface); border: none; color: var(--text-primary); padding: 0.75rem 1rem; cursor: pointer;">-</button>
                        <input type="number" id="qty" name="quantity" value="{{ $detailedProduct->min_qty }}" min="{{ $detailedProduct->min_qty }}" style="background: transparent; border: none; color: var(--text-primary); text-align: center; width: 60px; outline: none; font-size: 1.125rem;">
                        <button type="button" onclick="let q=document.getElementById('qty'); q.value=parseInt(q.value)+1;" style="background: var(--bg-surface); border: none; color: var(--text-primary); padding: 0.75rem 1rem; cursor: pointer;">+</button>
                    </div>

                    <button type="button" class="btn-primary" style="flex-grow: 1; padding: 1rem; font-size: 1.125rem;" onclick="submitAddToCart({{ $detailedProduct->id }})">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        {{ translate('Add to Cart') }}
                    </button>
                </div>
            </form>
            
            <script>
                function submitAddToCart(productId) {
                    const form = document.getElementById('add-to-cart-form');
                    const formData = new FormData(form);
                    
                    let variantParts = [];
                    
                    // Colors
                    if(formData.has('color')) {
                        variantParts.push(formData.get('color'));
                    }
                    
                    // Attributes
                    for(let [name, value] of formData.entries()) {
                        if(name.startsWith('attribute_id_')) {
                            variantParts.push(value);
                        }
                    }
                    
                    let variantString = variantParts.join('-');
                    let quantity = formData.get('quantity');
                    
                    animazonApi.addToCart(productId, quantity, variantString);
                }
            </script>



            <!-- Description -->
            <div>
                <h3 style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">{{ translate('Product Description') }}</h3>
                <div style="color: var(--text-secondary); line-height: 1.8;">
                    {!! $detailedProduct->getTranslation('description') !!}
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
