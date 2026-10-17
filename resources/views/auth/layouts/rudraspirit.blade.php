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

        body, html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #ffffff;
            background-color: #07090f;
            overflow-x: hidden;
        }

        /* Fullscreen Hero Background with Sacred Ambient Lighting */
        .spiritual-auth-page {
            position: relative;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            box-sizing: border-box;
            background-color: #07090f;
            background-image: 
                radial-gradient(circle at 50% 28%, rgba(212, 175, 55, 0.2) 0%, transparent 58%),
                linear-gradient(180deg, rgba(7, 9, 15, 0.58) 0%, rgba(10, 7, 14, 0.5) 50%, rgba(5, 5, 8, 0.85) 100%),
                url("{{ static_asset('images/auth/spiritual-login-bg.webp') }}");
            background-size: cover;
            background-position: center 30%;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* USER AUTH BG = Golden Dawn Himalayan Monastery with Glowing Oil Lamps */
        .spiritual-auth-page.user-page {
            background-image: 
                radial-gradient(circle at 50% 25%, rgba(212, 175, 55, 0.24) 0%, transparent 60%),
                linear-gradient(180deg, rgba(7, 9, 15, 0.48) 0%, rgba(10, 7, 14, 0.4) 50%, rgba(5, 5, 8, 0.8) 100%),
                url("{{ static_asset('images/auth/spiritual-user-bg.webp') }}") !important;
            background-size: cover;
            background-position: center 35%;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Fallbacks if webp not supported */
        @supports not (background-image: url("test.webp")) {
            .spiritual-auth-page {
                background-image: 
                    linear-gradient(180deg, rgba(7, 9, 15, 0.58) 0%, rgba(10, 7, 14, 0.5) 50%, rgba(5, 5, 8, 0.85) 100%),
                    url("{{ static_asset('images/auth/spiritual-login-bg.jpg') }}");
            }
            .spiritual-auth-page.user-page {
                background-image: 
                    linear-gradient(180deg, rgba(7, 9, 15, 0.48) 0%, rgba(10, 7, 14, 0.4) 50%, rgba(5, 5, 8, 0.8) 100%),
                    url("{{ static_asset('images/auth/spiritual-user-bg.jpg') }}") !important;
            }
        }

        /* Ambient Celestial Aura Glow */
        .spiritual-auth-page::before {
            content: '';
            position: fixed;
            top: -15%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.24) 0%, rgba(111, 15, 19, 0.18) 45%, transparent 75%);
            pointer-events: none;
            z-index: 0;
            filter: blur(80px);
        }

        /* True Luxury Frosted Glassmorphism Card Container (Golden Ratio min(880px, 94vw)) */
        .spiritual-glass-card {
            position: relative;
            z-index: 10;
            width: min(880px, 94vw);
            margin: 0 auto;
            background: linear-gradient(135deg, rgba(20, 15, 28, 0.46) 0%, rgba(10, 8, 16, 0.36) 100%) !important;
            backdrop-filter: blur(28px) saturate(210%) !important;
            -webkit-backdrop-filter: blur(28px) saturate(210%) !important;
            border: 1px solid rgba(255, 255, 255, 0.22) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.4) !important;
            border-left: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 26px !important;
            box-shadow: 
                0 32px 85px rgba(0, 0, 0, 0.75),
                0 0 50px rgba(212, 175, 55, 0.14),
                inset 0 1px 2px rgba(255, 255, 255, 0.35) !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .spiritual-glass-card:hover {
            box-shadow: 
                0 36px 95px rgba(0, 0, 0, 0.85),
                0 0 60px rgba(212, 175, 55, 0.2),
                inset 0 1px 2px rgba(255, 255, 255, 0.45) !important;
        }

        /* Left Side Consecrated Artwork Panel */
        .spiritual-left-panel {
            position: relative;
            min-height: 560px;
            background-color: transparent;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            padding: 40px 32px;
            box-sizing: border-box;
        }

        .spiritual-panel-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            filter: brightness(0.88) contrast(1.08);
            transform: scale(1.02);
            transition: transform 6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .spiritual-glass-card:hover .spiritual-panel-img {
            transform: scale(1.05);
        }

        .spiritual-left-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, 
                rgba(8, 6, 12, 0.12) 0%, 
                rgba(10, 8, 14, 0.55) 50%, 
                rgba(8, 6, 12, 0.94) 100%);
            z-index: 1;
        }

        .spiritual-left-content {
            position: relative;
            z-index: 2;
            max-width: 320px;
        }

        .spiritual-mantra-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: rgba(212, 175, 55, 0.18);
            border: 1px solid rgba(212, 175, 55, 0.45);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #fae498;
            margin-bottom: 12px;
            backdrop-filter: blur(8px);
            text-transform: uppercase;
        }

        .spiritual-left-content h2 {
            font-family: 'Cinzel', Georgia, serif;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #ffffff;
            margin: 0 0 10px;
            line-height: 1.3;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.85);
            word-break: break-word;
        }

        .spiritual-left-content p {
            font-size: 12.5px;
            line-height: 1.6;
            color: #e5ded3;
            margin: 0 0 16px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
        }

        .spiritual-badges-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .spiritual-pill {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.05em;
            padding: 4px 9px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 6px;
            color: #fae498;
            backdrop-filter: blur(6px);
        }

        /* Right Side Form Panel — Transparent Glass with generous padding */
        .spiritual-right-panel {
            padding: 44px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: transparent !important;
            box-sizing: border-box;
        }

        /* Centered & Refined Narrow Form Box (Max 310px width!) */
        .spiritual-form-box {
            width: 100%;
            max-width: 310px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            box-sizing: border-box;
        }

        /* Brand Crest Header — Larger Sacred Logo with Radiant Glow */
        .spiritual-brand-header {
            text-align: center;
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .spiritual-logo-wrapper {
            position: relative;
            width: 86px;
            height: 86px;
            margin-bottom: 12px;
            display: grid;
            place-items: center;
        }

        .spiritual-logo-glow {
            position: absolute;
            inset: -10px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.5) 0%, rgba(111, 15, 19, 0.25) 50%, transparent 75%);
            border-radius: 50%;
            filter: blur(14px);
            z-index: 1;
        }

        .spiritual-logo-wrapper img {
            position: relative;
            z-index: 2;
            width: 78px;
            height: 78px;
            object-fit: contain;
            filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.75));
            transition: transform 0.3s ease;
        }

        .spiritual-logo-wrapper:hover img {
            transform: scale(1.05);
        }

        .spiritual-brand-header h1 {
            font-family: 'Cinzel', Georgia, serif;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #fae498;
            margin: 0 0 4px;
            text-transform: uppercase;
            text-shadow: 0 2px 14px rgba(212, 175, 55, 0.4);
        }

        .spiritual-brand-header p {
            font-size: 12px;
            color: #d1c7b7;
            margin: 0;
            letter-spacing: 0.02em;
        }

        .spiritual-form {
            width: 100%;
        }

        /* Glassmorphic Form Inputs */
        .spiritual-form .form-group {
            margin-bottom: 14px;
            width: 100%;
        }

        .spiritual-form label {
            display: block;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: #e8d7b8;
            margin-bottom: 5px;
        }

        .spiritual-input-wrap {
            position: relative;
            width: 100%;
        }

        .spiritual-input-wrap .form-control,
        .spiritual-form input.form-control {
            width: 100% !important;
            height: 42px;
            background: rgba(255, 255, 255, 0.07) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(212, 175, 55, 0.35) !important;
            border-radius: 10px !important;
            color: #ffffff !important;
            padding: 8px 14px;
            font-size: 13px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3), 0 2px 6px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.25s ease;
            box-sizing: border-box;
        }

        .spiritual-input-wrap .form-control:focus,
        .spiritual-form input.form-control:focus {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: #fae498 !important;
            box-shadow: 0 0 16px rgba(212, 175, 55, 0.35), inset 0 1px 2px rgba(255, 255, 255, 0.25) !important;
            outline: none !important;
        }

        .spiritual-input-wrap .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #fae498;
            cursor: pointer;
            font-size: 17px;
            z-index: 5;
            transition: opacity 0.2s, transform 0.2s;
            opacity: 0.85;
        }

        .spiritual-input-wrap .password-toggle:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        /* Custom Elegant Checkbox & Remember Me Row */
        .spiritual-meta-row {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 10px 0 18px;
            font-size: 12px;
        }

        .spiritual-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #d1c7b7;
            cursor: pointer;
            font-size: 11.5px;
            letter-spacing: 0.04em;
            user-select: none;
            margin: 0;
        }

        .spiritual-checkbox input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 1.5px solid rgba(212, 175, 55, 0.6);
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.07);
            margin: 0;
            padding: 0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .spiritual-checkbox input[type="checkbox"]:checked {
            background: #9b1c23;
            border-color: #fae498;
        }

        .spiritual-checkbox input[type="checkbox"]:checked::after {
            content: "";
            width: 4px;
            height: 8px;
            border: solid #fae498;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            margin-bottom: 2px;
            display: block;
        }

        .spiritual-forgot-link {
            color: #fae498;
            text-decoration: none;
            font-weight: 500;
            font-size: 11.5px;
            transition: color 0.2s ease, text-shadow 0.2s ease;
        }

        .spiritual-forgot-link:hover {
            color: #ffffff;
            text-decoration: underline;
            text-shadow: 0 0 8px rgba(212, 175, 55, 0.6);
        }

        /* Cute, Refined, Compact Button Wrap */
        .spiritual-btn-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 14px;
            width: 100%;
        }

        /* Gold and Crimson Consecrated Compact Button */
        .spiritual-btn-submit {
            position: relative;
            width: auto !important;
            min-width: 180px;
            max-width: 210px;
            height: 40px;
            padding: 0 20px;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px;
            background: linear-gradient(135deg, #7c1218 0%, #9b1c23 45%, #6f0e13 100%) !important;
            border: 1px solid rgba(250, 228, 152, 0.6) !important;
            border-radius: 999px !important;
            color: #ffffff !important;
            font-family: 'Cinzel', Georgia, serif !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.1em !important;
            text-transform: uppercase !important;
            cursor: pointer !important;
            box-shadow: 
                0 10px 24px rgba(124, 18, 24, 0.55),
                0 0 16px rgba(212, 175, 55, 0.3),
                inset 0 1px 1px rgba(255, 255, 255, 0.45) !important;
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
            overflow: hidden;
            text-decoration: none;
        }

        .spiritual-btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.28), transparent);
            transition: left 0.6s ease;
        }

        .spiritual-btn-submit:hover {
            transform: translateY(-2px) scale(1.02);
            background: linear-gradient(135deg, #8c151c 0%, #b22129 45%, #6f0e13 100%) !important;
            border-color: #fae498 !important;
            box-shadow: 
                0 12px 28px rgba(124, 18, 24, 0.7),
                0 0 20px rgba(212, 175, 55, 0.45),
                inset 0 1px 1px rgba(255, 255, 255, 0.55) !important;
        }

        .spiritual-btn-submit:hover::after {
            left: 100%;
        }

        /* Secondary actions & links */
        .spiritual-alt-action {
            margin-top: 16px;
            text-align: center;
            font-size: 12px;
            color: #d1c7b7;
            width: 100%;
        }

        .spiritual-alt-action a {
            color: #fae498;
            font-weight: 600;
            margin-left: 4px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .spiritual-alt-action a:hover {
            color: #ffffff;
            text-decoration: underline;
            text-shadow: 0 0 8px rgba(212, 175, 55, 0.6);
        }

        .spiritual-back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #fae498;
            font-size: 11.5px;
            font-weight: 500;
            text-decoration: none;
            margin-top: 18px;
            transition: all 0.2s ease;
            width: 100%;
        }

        .spiritual-back-link:hover {
            color: #ffffff;
            text-shadow: 0 0 8px rgba(212, 175, 55, 0.5);
        }

        @media (max-width: 991px) {
            .spiritual-glass-card {
                width: min(440px, 92vw);
                border-radius: 20px !important;
            }
            .spiritual-right-panel {
                padding: 36px 24px;
            }
            .spiritual-form-box {
                max-width: 100%;
            }
        }
    </style>

    <script>
        var AIZ = AIZ || {};
    </script>
</head>
<body>
    @yield('content')

    <!-- Scripts -->
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script src="{{ static_asset('assets/js/aiz-core.js') }}"></script>

    @yield('script')

    <script type="text/javascript">
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach
    </script>
</body>
</html>
