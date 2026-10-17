<!DOCTYPE html>

@php
    $rtl = get_session_language()->rtl;
@endphp

@if ($rtl == 1)
    <html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description', 'Authentic certified Nepal Rudraksha beads, energized malas, and sacred spiritual items.'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords', 'rudraksha, nepal rudraksha, shivarudrakshainc, energized mala, 1-21 mukhi'))">
    <meta name="theme-color" content="#4f080c">
    <title>@yield('meta_title', 'Shiva Rudraksha Inc. | Sacred Portal')</title>

    <!-- Favicon -->
    @php
        $site_icon = static_asset('images/brand/shiva-rudraksha-logo.png');
        if (get_setting('site_icon')) {
            $uploaded = uploaded_asset(get_setting('site_icon'));
            if ($uploaded) { $site_icon = $uploaded; }
        }
    @endphp
    <link rel="icon" href="{{ $site_icon }}">
    <link rel="apple-touch-icon" href="{{ $site_icon }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css?v=') }}{{ rand(1000, 9999) }}">
    
    <style>
        :root{
            --maroon: #6f0f13;
            --maroon-dark: #4f080c;
            --maroon-glow: #8e181d;
            --gold: #d4af37;
            --gold-light: #fae498;
            --gold-dark: #b8860b;
            --primary: #7a1116;
            --hov-primary: #981c22;
            --soft-primary: rgba(122, 17, 22, 0.2);
            --dark: #0a0e17;
            --text-gold: #fae498;
            --text-muted: #d1c7b7;
        }

        /* Full Spiritual Body & Atmospheric Kailash Backdrop */
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #080a10;
            color: #f7f3eb;
            overflow-x: hidden;
        }

        .spiritual-auth-page {
            position: relative;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px 16px;
            background-color: #080a10;
            background-image: 
                radial-gradient(circle at 50% 25%, rgba(212, 175, 55, 0.18) 0%, transparent 60%),
                linear-gradient(180deg, rgba(8, 10, 18, 0.72) 0%, rgba(12, 8, 16, 0.65) 50%, rgba(6, 6, 10, 0.92) 100%),
                url("{{ static_asset('images/auth/spiritual-login-bg.webp') }}");
            background-size: cover;
            background-position: center 30%;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Fallback if webp not supported */
        @supports not (background-image: url("test.webp")) {
            .spiritual-auth-page {
                background-image: 
                    linear-gradient(180deg, rgba(8, 10, 18, 0.72) 0%, rgba(12, 8, 16, 0.65) 50%, rgba(6, 6, 10, 0.92) 100%),
                    url("{{ static_asset('images/auth/spiritual-login-bg.jpg') }}");
            }
        }

        /* Ambient Celestial Aura Glow */
        .spiritual-auth-page::before {
            content: '';
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.22) 0%, rgba(111, 15, 19, 0.18) 45%, transparent 75%);
            pointer-events: none;
            z-index: 0;
            filter: blur(80px);
        }

        /* Glassmorphism Main Card Container */
        .spiritual-glass-card {
            position: relative;
            z-index: 10;
            width: min(1040px, 100%);
            background: rgba(14, 11, 20, 0.74) !important;
            backdrop-filter: blur(28px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(28px) saturate(180%) !important;
            border: 1px solid rgba(212, 175, 55, 0.42) !important;
            border-radius: 24px !important;
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.85),
                0 0 50px rgba(212, 175, 55, 0.12),
                inset 0 1px 1px rgba(255, 255, 255, 0.28) !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .spiritual-glass-card:hover {
            box-shadow: 
                0 34px 90px rgba(0, 0, 0, 0.9),
                0 0 65px rgba(212, 175, 55, 0.18),
                inset 0 1px 1px rgba(255, 255, 255, 0.35) !important;
        }

        /* Compact card variant for single column views */
        .spiritual-glass-card.compact {
            width: min(520px, 100%);
            padding: 44px 38px;
        }

        /* Left Side Consecrated Artwork */
        .spiritual-left-panel {
            position: relative;
            min-height: 580px;
            background-color: #0d0a12;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            padding: 44px 38px;
        }

        .spiritual-panel-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            filter: brightness(0.85) contrast(1.1);
            transform: scale(1.02);
            transition: transform 6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .spiritual-glass-card:hover .spiritual-panel-img {
            transform: scale(1.06);
        }

        .spiritual-left-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, 
                rgba(10, 8, 14, 0.25) 0%, 
                rgba(10, 8, 14, 0.65) 50%, 
                rgba(8, 6, 12, 0.96) 100%);
            z-index: 1;
        }

        .spiritual-left-content {
            position: relative;
            z-index: 2;
        }

        .spiritual-mantra-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(212, 175, 55, 0.18);
            border: 1px solid rgba(212, 175, 55, 0.45);
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: #fae498;
            margin-bottom: 16px;
            backdrop-filter: blur(8px);
            text-transform: uppercase;
        }

        .spiritual-left-content h2 {
            font-family: 'Cinzel', Georgia, serif;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #ffffff;
            margin: 0 0 12px;
            line-height: 1.25;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.8);
        }

        .spiritual-left-content p {
            font-size: 13.5px;
            line-height: 1.65;
            color: #e5ded3;
            margin: 0 0 22px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
        }

        .spiritual-badges-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .spiritual-pill {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            padding: 5px 11px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: #fae498;
            backdrop-filter: blur(6px);
        }

        /* Right Side Form Panel */
        .spiritual-right-panel {
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(14, 11, 20, 0.65);
        }

        /* Brand Crest Header */
        .spiritual-brand-header {
            text-align: center;
            margin-bottom: 26px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .spiritual-logo-wrapper {
            position: relative;
            width: 72px;
            height: 72px;
            margin-bottom: 14px;
            display: grid;
            place-items: center;
        }

        .spiritual-logo-glow {
            position: absolute;
            inset: -8px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.5) 0%, rgba(111, 15, 19, 0.28) 55%, transparent 75%);
            border-radius: 50%;
            filter: blur(12px);
            z-index: 1;
        }

        .spiritual-logo-wrapper img {
            position: relative;
            z-index: 2;
            width: 64px;
            height: 64px;
            object-fit: contain;
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.7));
        }

        .spiritual-brand-header h1 {
            font-family: 'Cinzel', Georgia, serif;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: 0.09em;
            color: #fae498;
            margin: 0 0 6px;
            text-transform: uppercase;
            text-shadow: 0 2px 14px rgba(212, 175, 55, 0.35);
        }

        .spiritual-brand-header p {
            font-size: 13px;
            color: #d1c7b7;
            margin: 0;
            letter-spacing: 0.02em;
        }

        /* Glassmorphic Form Inputs */
        .spiritual-form .form-group {
            margin-bottom: 20px;
        }

        .spiritual-form label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #e8d7b8;
            margin-bottom: 8px;
        }

        .spiritual-input-wrap {
            position: relative;
        }

        .spiritual-input-wrap .form-control,
        .spiritual-form input.form-control {
            height: 48px;
            background: rgba(255, 255, 255, 0.07) !important;
            border: 1px solid rgba(212, 175, 55, 0.35) !important;
            border-radius: 12px !important;
            color: #ffffff !important;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.25s ease;
        }

        .spiritual-input-wrap .form-control:focus,
        .spiritual-form input.form-control:focus {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: #fae498 !important;
            box-shadow: 0 0 18px rgba(212, 175, 55, 0.3) !important;
            outline: none !important;
        }

        .spiritual-input-wrap .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #fae498;
            cursor: pointer;
            font-size: 18px;
            z-index: 5;
            transition: opacity 0.2s;
        }

        .spiritual-input-wrap .password-toggle:hover {
            opacity: 0.8;
        }

        /* Remember Me & Meta Row */
        .spiritual-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 16px 0 24px;
            font-size: 13px;
        }

        .spiritual-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #d1c7b7;
            cursor: pointer;
            margin: 0;
            user-select: none;
        }

        .spiritual-checkbox input[type="checkbox"] {
            accent-color: #d4af37;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .spiritual-forgot-link {
            color: #fae498;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease, text-shadow 0.2s ease;
        }

        .spiritual-forgot-link:hover {
            color: #ffffff;
            text-decoration: underline;
            text-shadow: 0 0 8px rgba(212, 175, 55, 0.6);
        }

        /* Gold and Crimson Consecrated Submit Button */
        .spiritual-btn-submit {
            position: relative;
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #7c1218 0%, #9e1a22 50%, #5d0c10 100%) !important;
            border: 1px solid rgba(212, 175, 55, 0.65) !important;
            border-radius: 12px !important;
            color: #ffffff !important;
            font-family: 'Cinzel', Georgia, serif;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 
                0 10px 28px rgba(124, 18, 24, 0.55),
                inset 0 1px 1px rgba(255, 255, 255, 0.35) !important;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .spiritual-btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: left 0.6s ease;
        }

        .spiritual-btn-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #8c151c 0%, #b22129 45%, #6f0e13 100%) !important;
            border-color: #fae498 !important;
            box-shadow: 
                0 14px 34px rgba(124, 18, 24, 0.7),
                0 0 20px rgba(212, 175, 55, 0.4),
                inset 0 1px 1px rgba(255, 255, 255, 0.5) !important;
        }

        .spiritual-btn-submit:hover::after {
            left: 100%;
        }

        /* Secondary actions & links */
        .spiritual-alt-action {
            margin-top: 24px;
            text-align: center;
            font-size: 13.5px;
            color: #d1c7b7;
        }

        .spiritual-alt-action a {
            color: #fae498;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .spiritual-alt-action a:hover {
            color: #ffffff;
            text-decoration: underline;
            text-shadow: 0 0 8px rgba(212, 175, 55, 0.6);
        }

        /* Back Link */
        .spiritual-back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            color: #fae498;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .spiritual-back-link:hover {
            color: #ffffff;
            transform: translateX(-4px);
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.6);
        }

        /* Responsive Layout Adjustments */
        @media (max-width: 991px) {
            .spiritual-left-panel {
                min-height: 240px;
                padding: 28px 24px;
            }
            .spiritual-right-panel {
                padding: 34px 24px;
            }
            .spiritual-brand-header h1 {
                font-size: 18px;
            }
        }
    </style>

    @yield('css')
    <script>
        var AIZ = AIZ || {};
    </script>
</head>
<body>

    @yield('content')

    <!-- SCRIPTS -->
    @include('auth.login_register_js')

    @yield('script')

</body>
</html>
