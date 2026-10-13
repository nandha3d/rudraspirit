@extends('frontend.layouts.app')

@section('content')
@php
    $rsParent = rudraspirit_root_category();
    $rsParentId = $rsParent ? $rsParent->id : 108;

    // Load subcategories from database
    $rsCategories = \App\Models\Category::where('parent_id', $rsParentId)->orderBy('order_level')->get();

    // Load all published Rudraksha products with relationships
    $rsDbProducts = \App\Models\Product::where('published', 1)
        ->where('category_id', '!=', 109) // exclude demo Supplements
        ->with(['categories', 'main_category', 'product_translations'])
        ->orderBy('id')
        ->get();
@endphp

<main class="page-shell shop-page" style="padding-top:50px;padding-bottom:90px;">
    <div class="container">
        <div class="page-heading">
            <span>SHOP</span>
            <h1>The full collection</h1>
            <p>Every bead is individually inspected and laboratory certified with X-ray verification. Prices are in {{ get_system_currency()->code ?? 'CAD' }}.</p>
        </div>

        <!-- Catalogue controls: Search & Category Pills -->
        <div class="catalogue-controls" style="margin-bottom:38px;">
            <label class="search-box">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input id="shop-catalog-search" oninput="filterShopList()" placeholder="Search products, mukhis, deities or benefits...">
            </label>
            <div class="category-pills" id="shop-category-pills">
                <button type="button" class="selected" onclick="filterCategory(this, 'all')">All</button>
                @foreach ($rsCategories as $cat)
                    <button type="button" onclick="filterCategory(this, '{{ $cat->slug }}')">{{ $cat->getTranslation('name') }}</button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid (Connected to Database & Cart) -->
        <div class="product-grid lovable-grid" id="main-product-grid">
            @foreach ($rsDbProducts as $product)
                @php
                    $catSlugs = $product->categories->pluck('slug')->toArray();
                    if ($product->main_category) {
                        $catSlugs[] = $product->main_category->slug;
                    }
                    $catSlugsStr = implode(' ', array_filter(array_unique($catSlugs)));

                    $cardImg = uploaded_asset($product->thumbnail_img);
                    if (!$cardImg || strpos($cardImg, 'placeholder') !== false) {
                        $cardImg = asset('images/products/1-mukhi-nepal-rudraksha/top.jpg');
                    }

                    $productName = $product->getTranslation('name');
                    $productDesc = $product->getTranslation('description') ?: 'Authentic holy Rudraksha bead, laboratory certified with individual X-ray test report.';
                    $origin = (stripos($catSlugsStr, 'indonesia') !== false || stripos($productName, 'indonesia') !== false) ? 'Indonesia' : 'Nepal';
                    $waMessage = rawurlencode("Hello Shiva Rudraksha Inc., I am interested in " . $productName . ". Please share size availability and certificate details.");
                @endphp

                <article class="product-card" data-categories="{{ $catSlugsStr }}" data-name="{{ strtolower($productName) }} {{ strtolower($productDesc) }}">
                    <a href="{{ route('product', $product->slug) }}" class="card-open" style="display:block;text-decoration:none;">
                        <div class="gallery-slider">
                            <div class="gallery-image-wrap">
                                <img src="{{ $cardImg }}" alt="{{ $productName }}" onerror="this.onerror=null;this.src='{{ asset('images/products/1-mukhi-nepal-rudraksha/top.jpg') }}';" loading="lazy">
                            </div>
                        </div>
                    </a>

                    <div class="card-content">
                        <div class="card-badges">
                            <span>CERTIFIED</span>
                            <span>{{ $product->current_stock > 0 ? 'IN STOCK' : 'AVAILABLE' }}</span>
                        </div>

                        <a href="{{ route('product', $product->slug) }}" class="product-name" style="display:block;text-decoration:none;">
                            {{ $productName }}
                        </a>

                        <p style="min-height:42px;font-size:14px;color:var(--muted);margin:8px 0 14px;line-height:1.5;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($productDesc), 120) }}
                        </p>

                        <div class="card-meta" style="display:flex;gap:14px;font-size:12px;color:var(--muted);margin-bottom:14px;">
                            <span style="display:flex;align-items:center;gap:4px;">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                {{ $origin }}
                            </span>
                            <span style="display:flex;align-items:center;gap:4px;">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                Certificate Included
                            </span>
                        </div>

                        <div class="card-bottom">
                            <div>
                                <small style="font-size:11px;color:var(--muted);text-transform:uppercase;">Price</small>
                                <strong style="color:var(--maroon);font-size:14px;font-family:Georgia,serif;line-height:1.2;">On Request</strong>
                            </div>

                            <a href="{{ route('product', $product->slug) }}" class="details-button" style="text-decoration:none;">
                                View Details
                            </a>

                            <a href="https://wa.me/14372671257?text={{ $waMessage }}" target="_blank" rel="noreferrer" class="enquire-button" style="text-decoration:none;background:var(--maroon);color:#fff;" title="Enquire on WhatsApp">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                Enquire
                            </a>
                        </div>

                        <!-- Contact for Details CTA -->
                        <div style="margin-top:10px;">
                            <a href="https://wa.me/14372671257?text={{ $waMessage }}" target="_blank" rel="noreferrer" class="maroon-button" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;text-decoration:none;font-size:14px;min-height:44px;border-radius:999px;">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                Contact for Details &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</main>

<script>
var currentCategory = 'all';

function filterCategory(btn, categorySlug) {
    var buttons = document.querySelectorAll('#shop-category-pills button');
    buttons.forEach(function(b) { b.classList.remove('selected'); });
    if (btn) btn.classList.add('selected');
    currentCategory = (categorySlug || 'all').toLowerCase().trim();
    filterShopList();
}

function filterShopList() {
    var query = (document.getElementById('shop-catalog-search')?.value || '').toLowerCase().trim();
    var cards = document.querySelectorAll('#main-product-grid .product-card');

    cards.forEach(function(card) {
        var cardCats = (card.getAttribute('data-categories') || '').toLowerCase().split(' ');
        var cardName = (card.getAttribute('data-name') || '').toLowerCase();

        var matchesCategory = (currentCategory === 'all' || cardCats.includes(currentCategory));
        var matchesQuery = !query || cardName.includes(query);

        if (matchesCategory && matchesQuery) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Check URL query parameters on load (e.g. ?category=nepal-rudraksha or ?category=Nepal+Rudraksha)
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var catParam = urlParams.get('category');
    if (catParam) {
        var normalized = catParam.trim().toLowerCase().replace(/\s+/g, '-').replace(/&/g, '');
        var buttons = document.querySelectorAll('#shop-category-pills button');
        buttons.forEach(function(b) {
            var bText = b.textContent.trim().toLowerCase().replace(/\s+/g, '-').replace(/&/g, '');
            var bAttr = (b.getAttribute('onclick') || '').toLowerCase();
            if (bText === normalized || bAttr.includes("'" + normalized + "'") || bAttr.includes('"' + normalized + '"')) {
                filterCategory(b, normalized);
            }
        });
    }
});
</script>
@endsection
