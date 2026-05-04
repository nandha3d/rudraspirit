<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('meta_title', get_setting('website_name'))</title>
    
    <meta name="description" content="@yield('meta_description', get_setting('meta_description'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords'))">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/animazon.css') }}">
    @yield('css')
</head>
<body>

    <!-- Header -->
    <header class="animazon-header">
        <div class="animazon-header-inner">
            <a href="{{ route('home') }}" class="animazon-logo">
                @if(get_setting('header_logo') != null)
                    <img src="{{ uploaded_asset(get_setting('header_logo')) }}" alt="{{ get_setting('website_name') }}">
                @else
                    <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ get_setting('website_name') }}">
                @endif
                <span>{{ get_setting('website_name') }}</span>
            </a>

            <nav class="animazon-nav">
                <a href="{{ route('home') }}" class="animazon-nav-link">{{ translate('Home') }}</a>
                <a href="{{ route('categories.all') }}" class="animazon-nav-link">{{ translate('Categories') }}</a>
                <a href="{{ route('brands.all') }}" class="animazon-nav-link">{{ translate('Brands') }}</a>
                @if(addon_is_activated('flash_deal'))
                <a href="{{ route('flash-deals') }}" class="animazon-nav-link">{{ translate('Flash Deals') }}</a>
                @endif
            </nav>

            <div class="animazon-header-actions">
                <a href="{{ route('search') }}" class="btn-icon" aria-label="Search">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </a>
                
                <a href="{{ route('user.login') }}" class="btn-icon" aria-label="Account">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>

                <a href="{{ route('cart') }}" class="animazon-cart-btn" aria-label="Cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <span class="animazon-cart-count" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="animazon-main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="animazon-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="animazon-logo" style="margin-bottom: 1rem;">
                    @if(get_setting('footer_logo') != null)
                        <img src="{{ uploaded_asset(get_setting('footer_logo')) }}" alt="{{ get_setting('website_name') }}">
                    @else
                        <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ get_setting('website_name') }}">
                    @endif
                    <span>{{ get_setting('website_name') }}</span>
                </a>
                <p style="color: var(--text-secondary); line-height: 1.6;">
                    {{ get_setting('about_us_description', 'Your premium shopping destination for the best products.') }}
                </p>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">{{ translate('Contact Info') }}</h4>
                <ul class="footer-links">
                    <li><span style="color: var(--text-secondary)">{{ translate('Address') }}:</span> {{ get_setting('contact_address') }}</li>
                    <li><span style="color: var(--text-secondary)">{{ translate('Phone') }}:</span> <a href="tel:{{ get_setting('contact_phone') }}">{{ get_setting('contact_phone') }}</a></li>
                    <li><span style="color: var(--text-secondary)">{{ translate('Email') }}:</span> <a href="mailto:{{ get_setting('contact_email') }}">{{ get_setting('contact_email') }}</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">{{ translate('Quick Links') }}</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">{{ translate('Home') }}</a></li>
                    <li><a href="{{ route('sellerpolicy') }}">{{ translate('Seller Policy') }}</a></li>
                    <li><a href="{{ route('returnpolicy') }}">{{ translate('Return Policy') }}</a></li>
                    <li><a href="{{ route('supportpolicy') }}">{{ translate('Support Policy') }}</a></li>
                    <li><a href="{{ route('terms') }}">{{ translate('Terms & Conditions') }}</a></li>
                    <li><a href="{{ route('privacypolicy') }}">{{ translate('Privacy Policy') }}</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">{{ translate('My Account') }}</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('user.login') }}">{{ translate('Login') }}</a></li>
                    <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard') }}</a></li>
                    <li><a href="{{ route('cart') }}">{{ translate('My Cart') }}</a></li>
                    <li><a href="{{ route('orders.track') }}">{{ translate('Track Order') }}</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            &copy; {{ date('Y') }} {{ get_setting('website_name') }}. {{ translate('All Rights Reserved.') }}
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Set up global config for AJAX API calls
        window.animazonConfig = {
            csrfToken: '{{ csrf_token() }}',
            v3ApiUrl: '{{ url('/api/v3') }}',
            isLoggedIn: {{ Auth::check() ? 'true' : 'false' }},
        };
    </script>
    <script src="{{ static_asset('assets/js/animazon.js') }}"></script>
    @yield('script')
</body>
</html>
