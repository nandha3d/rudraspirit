@php
    $rsParent = rudraspirit_root_category();
    $rsMukhiProducts = $rsParent ? \App\Models\Product::where('category_id', $rsParent->id)->where('published', 1)->orderBy('id')->get() : collect();
    $rsTopBanners = \App\Models\TopBanner::where('status', 1)->orderBy('id', 'desc')->get();
    $rsCartCount = count(get_user_cart());

    $rsCurrentCategorySlug = request()->route('category_slug');
    $rsInRudrakshaTree = $rsParent && $rsCurrentCategorySlug && $rsCurrentCategorySlug == $rsParent->slug;
@endphp

<style>
    .aiz-rudraspirit {
        --rs-ink: {{ get_setting('rudraspirit_color_ink', '#241B12') }};
        --rs-gold: {{ get_setting('rudraspirit_color_gold', '#B4894A') }};
        --rs-gold-deep: {{ get_setting('rudraspirit_color_gold_deep', '#7A4E1E') }};
        --rs-dark: {{ get_setting('rudraspirit_color_dark', '#15110C') }};
        --rs-brown: {{ get_setting('rudraspirit_color_brown', '#3A2A1C') }};
    }
</style>

@if ($rsTopBanners->count() > 0)
<div class="rs-marquee-wrap">
    <div class="rs-marquee-track">
        @for ($i = 0; $i < 2; $i++)
            <div class="rs-marquee-group">
                @foreach ($rsTopBanners as $banner)
                    <span>&#10022;&nbsp; {{ $banner->getTranslation('text') }}</span>
                @endforeach
            </div>
        @endfor
    </div>
</div>
@endif

<header class="rs-header">
    <div class="rs-header-top">
        <a href="{{ route('home') }}" class="rs-logo">
            @if (get_setting('header_logo') != null)
                <img src="{{ uploaded_asset(get_setting('header_logo')) }}" alt="{{ get_setting('website_name') }}" class="rs-logo-img">
            @else
                <img src="{{ static_asset('assets/img/pages/rudraspirit/Logo_02-scaled.webp') }}" alt="{{ get_setting('website_name') }}" class="rs-logo-img">
            @endif
        </a>

        <div class="rs-header-search">
            <input id="search" name="search" placeholder="{{ translate('Search for a Rudraksha, mukhi or mala') }}…">
            <span class="rs-header-search-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <div class="typed-search-box d-none">
                <div class="search-preloader d-none"></div>
                <div class="search-nothing d-none" style="font-size:15px;color:var(--rs-ink-muted);padding:10px 16px;"></div>
                <div id="search-content"></div>
            </div>
        </div>

        <div class="rs-icons">
            @if (get_setting('show_currency_switcher') == 'on')
            <div class="rs-dropdown" id="currency-change">
                @php $rsCurrency = get_system_currency(); @endphp
                <span class="rs-dropdown-toggle">{{ $rsCurrency->code ?? '' }} <span style="font-size:9px;opacity:.6;">&#9660;</span></span>
                <div class="rs-dropdown-menu dropdown-menu">
                    @foreach (get_all_active_currency() as $currency)
                        <a href="javascript:void(0)" data-currency="{{ $currency->code }}" class="@if (isset($rsCurrency->code) && $rsCurrency->code == $currency->code) active @endif">
                            {{ $currency->code }} ({{ $currency->symbol }})
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Language switcher disabled in RudraSpirit header --}}

            @auth
                <a href="{{ route('dashboard') }}" class="rs-user" title="{{ translate('Account') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span class="rs-user-name">{{ Str::limit(Auth::user()->name, 14) }}</span>
                </a>
            @else
                <a href="javascript:void(0)" onclick="showLoginModal()" title="{{ translate('Account') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
            @endauth

            @if (Auth::check() && Auth::user()->user_type == 'customer')
                <a href="{{ route('wishlists.index') }}" title="{{ translate('Wishlist') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </a>
            @else
                <a href="javascript:void(0)" onclick="showLoginModal()" title="{{ translate('Wishlist') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </a>
            @endif

            <a href="javascript:void(0)" class="rs-cart-trigger" title="{{ translate('Cart') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span class="rs-cart-count cart-count">{{ $rsCartCount }}</span>
            </a>
            <div id="cart_items" class="d-none"></div>

            <a href="javascript:void(0)" class="rs-mobile-toggle" title="{{ translate('Menu') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </a>
        </div>
    </div>

    <nav class="rs-nav"><div class="rs-nav-inner">
        <a href="{{ route('home') }}" class="@if (request()->routeIs('home')) active @endif">{{ translate('Home') }}</a>
        <div class="rs-mega-trigger">
            <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}" class="@if ($rsInRudrakshaTree) active @endif">
                {{ translate('Nepal Rudraksha') }} <span style="font-size:11px;opacity:.6;">&#9660;</span>
            </a>
            @if ($rsMukhiProducts->count() > 0)
            <div class="rs-mega-panel">
                <div class="rs-mega-grid">
                    <div>
                        <div class="rs-mega-label">{{ translate('Rudraksha Types') }}</div>
                        <div class="rs-mega-list">
                            @foreach ($rsMukhiProducts as $rsMukhiProduct)
                                <a href="{{ route('mukhi.info', $rsMukhiProduct->slug) }}">
                                    @if ($rsMukhiProduct->thumbnail)
                                        <span class="rs-mega-bead" style="background-image:url('{{ get_image($rsMukhiProduct->thumbnail) }}');background-size:cover;"></span>
                                    @else
                                        <span class="rs-mega-bead"></span>
                                    @endif
                                    <span class="rs-mega-name">{{ $rsMukhiProduct->getTranslation('name') }}</span>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}" class="rs-mega-viewall">{{ translate('Shop All') }} {{ $rsMukhiProducts->count() }} {{ translate('Types') }} &rarr;</a>
                    </div>
                    <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}" class="rs-mega-promo">
                        <img class="rs-mega-promo-img" src="{{ static_asset('assets/img/pages/rudraspirit/Gemini_Generated_Image_9keguo9keguo9keg.webp') }}" alt="{{ translate('Rudraksha') }}">
                        <div class="rs-mega-promo-body">
                            <div class="rs-serif rs-mega-promo-title">{{ translate('Try our') }}<br><em>{{ translate('Rudraksha') }}</em></div>
                            <p class="rs-mega-promo-text">{{ translate('Hand-picked, lab-certified beads from the foothills of the Himalayas.') }}</p>
                            <span class="rs-btn rs-btn-light">{{ translate('Shop Now') }} <span>&rarr;</span></span>
                        </div>
                    </a>
                </div>
            </div>
            @endif
        </div>
        <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}" class="@if ($rsInRudrakshaTree) active @endif">{{ translate('Rudraksha Mala') }}</a>
        @auth
            <a href="{{ route('dashboard') }}" class="@if (request()->routeIs('dashboard') || request()->routeIs('profile')) active @endif">{{ translate('My Account') }}</a>
        @else
            <a href="javascript:void(0)" onclick="showLoginModal()">{{ translate('My Account') }}</a>
        @endauth
        <a href="{{ route('faq') }}" class="@if (request()->routeIs('faq')) active @endif">{{ translate('FAQ') }}</a>
        <a href="{{ route('blog') }}" class="@if (request()->routeIs('blog') || request()->routeIs('blog.details')) active @endif">{{ translate('Blog') }}</a>
        <a href="{{ route('rudraspirit.about') }}" class="@if (request()->routeIs('rudraspirit.about')) active @endif">{{ translate('About Us') }}</a>
        <a href="{{ route('custom-pages.show_custom_page', 'contact-us') }}" class="@if (request()->routeIs('custom-pages.show_custom_page') && request()->route('slug') == 'contact-us') active @endif">{{ translate('Contact Us') }}</a>
    </div></nav>
</header>

<!-- Mobile bottom tab bar -->
<nav class="rs-bottom-nav">
    <a href="{{ route('home') }}" class="rs-bottom-nav-item @if (request()->routeIs('home')) active @endif">
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        </span>
        <span>{{ translate('Home') }}</span>
    </a>
    @auth
        <a href="{{ route('dashboard') }}" class="rs-bottom-nav-item @if (request()->routeIs('dashboard') || request()->routeIs('profile')) active @endif">
    @else
        <a href="javascript:void(0)" onclick="showLoginModal()" class="rs-bottom-nav-item">
    @endauth
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </span>
        <span>{{ translate('Account') }}</span>
    </a>
    <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}" class="rs-bottom-nav-item @if ($rsInRudrakshaTree) active @endif">
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        </span>
        <span>{{ translate('Shop') }}</span>
    </a>
    <a href="javascript:void(0)" class="rs-bottom-nav-item rs-cart-trigger">
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <span class="rs-cart-count cart-count">{{ $rsCartCount }}</span>
        </span>
        <span>{{ translate('Cart') }}</span>
    </a>
</nav>

<!-- Cart drawer -->
<div class="rs-cart-overlay"></div>
<aside class="rs-cart-drawer">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:24px;border-bottom:1px solid var(--rs-cream-deep);">
        <span class="rs-serif" style="font-size:25px;color:var(--rs-ink);">{{ translate('Shopping Cart') }}</span>
        <span class="rs-cart-close" style="cursor:pointer;font-size:26px;color:var(--rs-ink-muted);">&times;</span>
    </div>
    <div class="rs-cart-drawer-body" style="flex:1;overflow-y:auto;padding:8px 24px;"></div>
    <div style="padding:22px 24px;border-top:1px solid var(--rs-cream-deep);">
        <div style="display:flex;justify-content:space-between;font-size:19px;color:var(--rs-ink);margin-bottom:16px;font-family:'Playfair Display',serif;">
            <span>{{ translate('Subtotal') }}</span>
            <span class="rs-cart-drawer-total"></span>
        </div>
        <a href="{{ route('cart') }}" class="rs-btn" style="display:block;text-align:center;margin-bottom:10px;">{{ translate('View Cart & Checkout') }}</a>
        <a href="javascript:void(0)" class="rs-cart-close" style="display:block;text-align:center;font-size:14px;letter-spacing:.16em;text-transform:uppercase;color:var(--rs-ink-soft);">{{ translate('Continue Shopping') }}</a>
    </div>
</aside>
