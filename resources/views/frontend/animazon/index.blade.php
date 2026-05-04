@extends('frontend.animazon.layouts.app')

@section('content')

<div class="animazon-container">
    
    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="hero-glow"></div>
        <div class="hero-content">
            <h1 class="hero-title">{{ translate('Discover Premium Products at') }} {{ get_setting('website_name') }}</h1>
            <p class="hero-subtitle">{{ get_setting('site_motto', 'Your ultimate shopping destination for exclusive deals and premium quality.') }}</p>
            <a href="{{ route('categories.all') }}" class="btn-primary">{{ translate('Shop Now') }}</a>
        </div>
        <img src="{{ static_asset('assets/img/hero-placeholder.png') }}" alt="Hero Image" class="hero-image" onerror="this.src='https://via.placeholder.com/400x400/1e293b/f8fafc?text=Animazon+Shopping'">
    </section>

    <!-- Featured Categories -->
    @if(isset($featured_categories) && count($featured_categories) > 0)
    <section class="mb-5">
        <h2 class="section-title">
            {{ translate('Featured Categories') }}
            <a href="{{ route('categories.all') }}" class="view-all">{{ translate('View All') }} &rarr;</a>
        </h2>
        
        <div class="category-grid">
            @foreach($featured_categories as $category)
                <a href="{{ route('products.category', $category->slug) }}" class="category-card">
                    @if($category->icon != null)
                        <img src="{{ uploaded_asset($category->icon) }}" alt="{{ $category->getTranslation('name') }}" class="category-icon">
                    @else
                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}" alt="{{ $category->getTranslation('name') }}" class="category-icon" style="border-radius: 50%;">
                    @endif
                    <div class="category-name">{{ $category->getTranslation('name') }}</div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Best Sellers (From AJAX or Loaded Directly) -->
    <!-- We'll load newest products via direct DB query here just for the demo, 
         since HomeController@index might not pass $newest_products directly. 
         Wait, HomeController@index doesn't pass products, it relies on AJAX for sections.
         Let's just call the V3 service here to keep it clean. -->
    @php
        $productService = app(\App\Services\Catalog\ProductCatalogService::class);
        $newestProducts = filter_products(\App\Models\Product::latest())->take(12)->get();
        $todaysDeals = filter_products(\App\Models\Product::where('todays_deal', 1))->take(12)->get();
    @endphp

    @if(count($todaysDeals) > 0)
    <section class="mb-5">
        <h2 class="section-title">
            {{ translate("Today's Deals") }}
            <a href="{{ route('flash-deals') }}" class="view-all">{{ translate('View All') }} &rarr;</a>
        </h2>
        <div class="product-grid">
            @foreach($todaysDeals as $product)
                @include('frontend.animazon.partials.product_card', ['product' => $product])
            @endforeach
        </div>
    </section>
    @endif

    @if(count($newestProducts) > 0)
    <section class="mb-5">
        <h2 class="section-title">
            {{ translate('New Arrivals') }}
            <a href="{{ route('search') }}" class="view-all">{{ translate('View All') }} &rarr;</a>
        </h2>
        <div class="product-grid">
            @foreach($newestProducts as $product)
                @include('frontend.animazon.partials.product_card', ['product' => $product])
            @endforeach
        </div>
    </section>
    @endif

</div>

@endsection
