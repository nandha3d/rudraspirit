@extends('frontend.layouts.app')

@section('content')
@php
    $rsParent = rudraspirit_root_category();
    // Featured products: 1 Mukhi (48), 4 Mukhi (51), 3 Mukhi (50) - 4 Mukhi used in place of 2 Mukhi as requested
    $rsFeaturedProducts = \App\Models\Product::where('published', 1)
        ->whereIn('id', [48, 51, 50])
        ->orderByRaw('FIELD(id, 48, 51, 50)')
        ->get();
    if ($rsFeaturedProducts->isEmpty()) {
        $rsFeaturedProducts = \App\Models\Product::where('published', 1)->orderBy('id')->take(3)->get();
    }
    
    // Load catalogue metadata
    $catalogueJsonPath = public_path('data/products.json');
    $catalogueProducts = file_exists($catalogueJsonPath) ? json_decode(file_get_contents($catalogueJsonPath), true) : [];

    $catalogueByMukhi = [];
    foreach ($catalogueProducts as $cp) {
        if (!empty($cp['mukhi'])) {
            $catalogueByMukhi[$cp['mukhi']] = $cp;
        }
    }
@endphp

<!-- HERO SECTION (Screenshot 1 / Original Repo Home) -->
<section class="lovable-hero">
    <div class="container lovable-hero-grid">
        <div>
            <span class="source-pill">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--orange);"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                DIRECTLY SOURCED • CERTIFIED
            </span>
            <h1>Authentic Certified Rudraksha from <em>Nepal</em>.</h1>
            <p>
                Carefully selected Rudraksha beads, malas and spiritual combinations—supplied in Canada, the United States and worldwide.
            </p>
            <div class="hero-actions">
                <a href="{{ route('rudraspirit.guide') }}" class="maroon-button" style="text-decoration:none;">
                    Rudraksha Guide
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
                <a href="{{ route('rudraspirit.recommendations') }}" class="outline-button large" style="text-decoration:none;">
                    Get a Recommendation
                </a>
            </div>
            <div class="promise-grid">
                <span>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--orange);"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Authentic Rudraksha
                </span>
                <span>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--orange);"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Certificate Available
                </span>
                <span>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--orange);"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Canada-Based Business
                </span>
                <span>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--orange);"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Worldwide Shipping
                </span>
                <span>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--orange);"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Personal Consultation
                </span>
            </div>
        </div>
        <div class="hero-product-panel">
            <img src="{{ asset('images/products/12-mukhi-nepal-rudraksha/top.jpg') }}" alt="Authentic Certified Nepal Rudraksha" onerror="this.onerror=null;this.src='{{ asset('shivarudraksha/images/products/12-mukhi-nepal-rudraksha/top.jpg') }}';">
        </div>
    </div>
</section>

<!-- FEATURED COLLECTION -->
<section class="home-featured container" style="padding-top:70px;padding-bottom:90px;">
    <div class="section-kicker">FEATURED COLLECTION</div>
    <h2>Discover authentic Nepal Rudraksha</h2>

    <!-- Product Grid (Top 3 Products: 1 Mukhi, 4 Mukhi, 3 Mukhi) -->
    <div class="product-grid" style="margin-top:28px;">
        @foreach ($rsFeaturedProducts as $product)
            @php
                $mukhiNum = function_exists('rudraspirit_mukhi_number') ? rudraspirit_mukhi_number($product) : null;
                $catMeta = ($mukhiNum && isset($catalogueByMukhi[$mukhiNum])) ? $catalogueByMukhi[$mukhiNum] : null;

                $cardImg = uploaded_asset($product->thumbnail_img);
                // 2nd image replacement guard: if 2-mukhi or broken, fallback to 4-mukhi
                if ($mukhiNum == 2) {
                    $cardImg = asset('images/products/4-mukhi-nepal-rudraksha/top.jpg');
                } elseif (!$cardImg || strpos($cardImg, 'placeholder') !== false) {
                    if ($catMeta && !empty($catMeta['images']['top'])) {
                        $cardImg = asset($catMeta['images']['top']);
                    } else {
                        $cardImg = asset('images/products/1-mukhi-nepal-rudraksha/top.jpg');
                    }
                }

                $productName = $product->getTranslation('name');
                $waMessage = rawurlencode("Hello Shiva Rudraksha Inc., I am interested in " . $productName . ". Please share size availability, certificate details and price.");
            @endphp

            <article class="product-card">
                <a href="{{ route('product', $product->slug) }}" class="card-open" style="display:block;text-decoration:none;">
                    <div class="gallery-slider">
                        <div class="gallery-image-wrap">
                            <img src="{{ $cardImg }}" alt="{{ $productName }}" onerror="this.onerror=null;this.src='{{ asset('images/products/4-mukhi-nepal-rudraksha/top.jpg') }}';" loading="lazy">
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
                        {{ $catMeta['description'] ?? 'Authentic holy Rudraksha bead, laboratory certified with individual X-ray test report.' }}
                    </p>

                    <div class="card-meta" style="display:flex;gap:14px;font-size:12px;color:var(--muted);margin-bottom:14px;">
                        <span style="display:flex;align-items:center;gap:4px;">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Nepal
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

                    {{-- Purchasing disabled on live site: prominent Contact for Details action --}}
                    <div style="margin-top:10px;">
                        <a href="https://wa.me/14372671257?text={{ $waMessage }}" target="_blank" rel="noreferrer" class="maroon-button" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;text-decoration:none;font-size:14px;min-height:44px;border-radius:999px;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            Contact for Details &rarr;
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<!-- Floating WhatsApp Button -->
<a class="floating-whatsapp" href="https://wa.me/14372671257" target="_blank" rel="noreferrer" title="Chat on WhatsApp">
    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
</a>
@endsection
