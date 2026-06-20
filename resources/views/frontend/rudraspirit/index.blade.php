@extends('frontend.layouts.app')

@section('content')
@php
    $rsParent = \App\Models\Category::where('slug', 'rudraksha-beads')->first();
    $rsMukhiPicks = $rsParent ? \App\Models\Product::where('category_id', $rsParent->id)->where('published', 1)->orderBy('id')->get() : collect();
    $rsTrendy = filter_products(\App\Models\Product::where('published', 1)->latest())->take(8)->get();
    $rsPosts = \App\Models\Blog::where('status', 1)->latest()->take(3)->get();
    $rsShopUrl = $rsParent ? route('products.category', $rsParent->slug) : route('categories.all');
    $rsMukhiCount = $rsMukhiPicks->count();
    $rsLowestPrice = $rsMukhiPicks->count() ? $rsMukhiPicks->min('unit_price') : null;

    $rsHeroSlides = [
        [
            'image' => 'Gemini_Generated_Image_9jfh569jfh569jfh.webp',
            'kicker' => translate('Nepal Origin · Lab Certified'),
            'title' => translate('Discover'),
            'title_em' => translate('Our Rudraksha'),
            'title_end' => '',
            'text' => translate('Embrace ancient wisdom and spirituality with our hand-picked, lab-certified Rudraksha beads sourced from the Himalayas.'),
            'cta' => translate('Shop Collection'),
        ],
        [
            'image' => 'Gemini_Generated_Image_uw5h9puw5h9puw5h.webp',
            'kicker' => translate('Blessed & Energised'),
            'title' => translate('Crafted'),
            'title_em' => translate('For Calm'),
            'title_end' => '',
            'text' => translate('Connect with the divine vibration of natural Rudraksha. Empower your journey and protect your aura.'),
            'cta' => translate('Shop Collection'),
        ],
        [
            'image' => 'Gemini_Generated_Image_9keguo9keguo9keg.webp',
            'kicker' => $rsMukhiCount > 0 ? $rsMukhiCount . ' ' . translate('Sacred Mukhi Types') : translate('Sacred Mukhi Types'),
            'title' => translate('Worn'),
            'title_em' => translate('With Intention'),
            'title_end' => '',
            'text' => $rsLowestPrice
                ? translate('From 1 Mukhi to 14 Mukhi — find the bead that resonates with your path, starting from') . ' ' . single_price($rsLowestPrice) . '.'
                : translate('From 1 Mukhi to 14 Mukhi — find the bead that resonates with your path, worn daily by thousands.'),
            'cta' => translate('View All Mukhis'),
        ],
    ];
@endphp

<!-- HERO -->
<section class="rs-hero">
    @foreach ($rsHeroSlides as $i => $slide)
        <div class="rs-hero-slide @if ($i == 0) active @endif" data-slide="{{ $i }}">
            <div class="rs-hero-content">
                <div class="rs-hero-kicker">{{ $slide['kicker'] }}</div>
                <h1 class="rs-hero-title">{{ $slide['title'] }} <em>{{ $slide['title_em'] }}</em> {{ $slide['title_end'] }}</h1>
                <p class="rs-hero-text">{{ $slide['text'] }}</p>
                <a href="{{ $rsShopUrl }}" class="rs-btn">{{ $slide['cta'] }} <span>&rarr;</span></a>
            </div>
            <div class="rs-hero-visual">
                <img src="{{ static_asset('assets/img/pages/rudraspirit/' . $slide['image']) }}" alt="{{ $slide['title'] }} {{ $slide['title_em'] }}">
            </div>
        </div>
    @endforeach
    <div class="rs-hero-dots">
        @foreach ($rsHeroSlides as $i => $slide)
            <button type="button" class="rs-hero-dot @if ($i == 0) active @endif" data-slide-target="{{ $i }}" aria-label="{{ translate('Slide') }} {{ $i + 1 }}"></button>
        @endforeach
    </div>
</section>

<!-- SHOP BY MUKHI -->
@if ($rsMukhiPicks->count() > 0)
<section class="rs-section">
    <h2 class="rs-section-title"><em>{{ translate('Shop By') }}</em> {{ translate('Mukhi') }}</h2>
    <p class="rs-section-sub">{{ translate('Explore our collection of sacred, certified beads — from single power beads to woven malas. Find the one that calls to you.') }}</p>
    <div class="rs-cat-rail">
        @foreach ($rsMukhiPicks as $rsMukhiPick)
            <a href="{{ route('product', $rsMukhiPick->slug) }}" class="rs-cat-item">
                <span class="rs-cat-circle">
                    @if ($rsMukhiPick->thumbnail)
                        <img src="{{ get_image($rsMukhiPick->thumbnail) }}" alt="{{ $rsMukhiPick->getTranslation('name') }}">
                    @else
                        <img src="{{ static_asset('assets/img/pages/rudraspirit/Gemini_Generated_Image_9jfh569jfh569jfh.webp') }}" alt="{{ $rsMukhiPick->getTranslation('name') }}">
                    @endif
                </span>
                <span class="rs-cat-name">{{ $rsMukhiPick->getTranslation('name') }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

<!-- BEST SELLERS BAND -->
<section class="rs-band">
    <h2 class="rs-section-title">{{ translate('Shop Our Best') }} <em>{{ translate('Sellers') }}</em></h2>
    <p class="rs-section-sub" style="margin-bottom:0;">{{ translate("Discover the beads our community returns to — from the calming 5 Mukhi to the obstacle-clearing Ganesha bead. These are the trusted favourites you'll want close.") }}</p>
</section>

<!-- TRENDY COLLECTION -->
@if ($rsTrendy->count() > 0)
<section class="rs-section">
    <h2 class="rs-section-title"><em>{{ translate('Trendy') }}</em> {{ translate('Collection') }}</h2>
    <p class="rs-section-sub">{{ translate('The beads our customers love most — from everyday companions to rare, high-mukhi treasures. Hand-selected and certified.') }}</p>
    <div class="rs-product-grid">
        @foreach ($rsTrendy as $product)
            @include('frontend.rudraspirit.partials.product_card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

<!-- LIMITED DEAL -->
<section class="rs-deal">
    <div class="rs-deal-content">
        <div class="rs-hero-kicker" style="color:var(--rs-gold-deep);">{{ translate("Don't Miss Out!") }}</div>
        <h2 class="rs-serif" style="font-weight:500;font-size:42px;letter-spacing:.06em;text-transform:uppercase;color:var(--rs-brown);margin:0 0 14px;"><em style="color:var(--rs-gold-deep);font-style:italic;">{{ translate('Limited') }}</em> {{ translate('Time Deal') }}</h2>
        <p style="font-size:17px;color:var(--rs-ink-soft);line-height:1.8;max-width:420px;margin:0 0 26px;">{{ translate("Exclusive savings on our most-loved certified beads. These blessings won't last long — begin your practice today.") }}</p>
        <div class="rs-deal-timer">
            <div><div id="rs-deal-days">00</div><span>{{ translate('Days') }}</span></div>
            <div><div id="rs-deal-hours">00</div><span>{{ translate('Hours') }}</span></div>
            <div><div id="rs-deal-mins">00</div><span>{{ translate('Mins') }}</span></div>
            <div><div id="rs-deal-secs">00</div><span>{{ translate('Secs') }}</span></div>
        </div>
        <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}" class="rs-btn">{{ translate('Shop Now') }} <span>&rarr;</span></a>
    </div>
    <div class="rs-deal-visual"></div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        rsStartCountdownBoxes(
            document.getElementById('rs-deal-days'), document.getElementById('rs-deal-hours'),
            document.getElementById('rs-deal-mins'), document.getElementById('rs-deal-secs'),
            1000 * 60 * 60 * 76
        );
    });
</script>

<!-- FEATURES -->
<section class="rs-features">
    <div class="rs-feature">
        <div class="rs-feature-icon">&#10022;</div>
        <div class="rs-feature-title">{{ translate('Free Shipping') }}</div>
        <div class="rs-feature-sub">{{ translate('On all certified orders worldwide') }}</div>
    </div>
    <div class="rs-feature">
        <div class="rs-feature-icon">&#10022;</div>
        <div class="rs-feature-title">{{ translate('Flexible Payment') }}</div>
        <div class="rs-feature-sub">{{ translate('Secured multi-currency checkout') }}</div>
    </div>
    <div class="rs-feature">
        <div class="rs-feature-icon">&#10022;</div>
        <div class="rs-feature-title">{{ translate('14-Day Returns') }}</div>
        <div class="rs-feature-sub">{{ translate('On unworn, certified beads') }}</div>
    </div>
    <div class="rs-feature">
        <div class="rs-feature-icon">&#10022;</div>
        <div class="rs-feature-title">{{ translate('Premium Support') }}</div>
        <div class="rs-feature-sub">{{ translate('Guidance from our bead experts') }}</div>
    </div>
</section>

<!-- BEHIND THE BRAND -->
@if ($rsPosts->count() > 0)
<section class="rs-section">
    <h2 class="rs-section-title"><em>{{ translate('Behind') }}</em> {{ translate('The Brand') }}</h2>
    <p class="rs-section-sub">{{ translate('Our journey, our values, and the stories woven through every certified bead. Discover what makes Rudra Spirit different.') }}</p>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:28px;text-align:left;">
        @foreach ($rsPosts as $post)
            <a href="{{ route('blog.details', $post->slug) }}" style="text-decoration:none;color:inherit;">
                <div style="position:relative;aspect-ratio:4/3;border-radius:8px;overflow:hidden;background:linear-gradient(150deg,var(--rs-tan-light),var(--rs-tan));">
                    @if ($post->banner)
                        <img src="{{ uploaded_asset($post->banner) }}" alt="{{ $post->title }}" style="width:100%;height:100%;object-fit:cover;">
                    @endif
                </div>
                <h3 class="rs-serif" style="font-size:22px;font-weight:500;color:var(--rs-ink);margin:18px 0 10px;">{{ $post->title }}</h3>
                <span style="font-size:13px;letter-spacing:.18em;text-transform:uppercase;color:var(--rs-gold);">{{ translate('Read More') }} &rarr;</span>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection
