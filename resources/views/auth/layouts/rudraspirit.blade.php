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
    <meta name="description" content="@yield('meta_description', get_setting('meta_description'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords'))">
    <title>@yield('meta_title', get_setting('website_name') . ' | ' . get_setting('site_motto'))</title>

    <!-- Favicon -->
    @php
        $site_icon = uploaded_asset(get_setting('site_icon'));
    @endphp
    <link rel="icon" href="{{ $site_icon }}">
    <link rel="apple-touch-icon" href="{{ $site_icon }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css?v=') }}{{ rand(1000, 9999) }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/rudraspirit.css?v=') }}{{ rand(1000, 9999) }}">

    <style>
        .aiz-rudraspirit {
            --rs-ink: {{ get_setting('rudraspirit_color_ink', '#241B12') }};
            --rs-gold: {{ get_setting('rudraspirit_color_gold', '#B4894A') }};
            --rs-gold-deep: {{ get_setting('rudraspirit_color_gold_deep', '#7A4E1E') }};
            --rs-dark: {{ get_setting('rudraspirit_color_dark', '#15110C') }};
            --rs-brown: {{ get_setting('rudraspirit_color_brown', '#3A2A1C') }};
        }
        body.aiz-rudraspirit {
            background: var(--rs-cream);
            min-height: 100vh;
        }
        .rs-auth-wrap {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .rs-auth-topbar {
            padding: 28px 40px;
        }
        .rs-auth-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--rs-ink);
        }
        .rs-auth-logo-img {
            height: 38px;
            width: auto;
        }
        .rs-auth-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 20px 60px;
        }
        .rs-auth-flash {
            width: 100%;
            max-width: 980px;
            margin-bottom: 16px;
        }
        .rs-auth-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 8px;
            border: 1px solid transparent;
        }
        .rs-auth-alert i {
            font-size: 18px;
            flex: 0 0 auto;
        }
        .rs-auth-alert-danger {
            background: #FDF0EF;
            border-color: #F2C6C2;
            color: #A13A31;
        }
        .rs-auth-alert-success {
            background: #F0F7F0;
            border-color: #BFDDC0;
            color: #2F6B33;
        }
        .rs-auth-alert-warning,
        .rs-auth-alert-info,
        .rs-auth-alert-dark {
            background: #FBF6EC;
            border-color: #E7D3AC;
            color: #7A5B1E;
        }
        .rs-auth-card {
            width: 100%;
            max-width: 980px;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(40,28,12,.12);
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .rs-auth-visual {
            position: relative;
            background: var(--rs-ink);
            min-height: 100%;
        }
        .rs-auth-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: .92;
        }
        .rs-auth-visual::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(21,17,12,0) 40%, rgba(21,17,12,.55) 100%);
        }
        .rs-auth-form-side {
            padding: 50px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .rs-auth-kicker {
            font-size: 13px;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--rs-gold-deep);
            margin-bottom: 10px;
        }
        .rs-auth-title {
            font-family: 'Playfair Display', serif;
            font-weight: 500;
            font-size: 30px;
            color: var(--rs-ink);
            margin: 0 0 8px;
        }
        .rs-auth-sub {
            font-size: 14px;
            color: var(--rs-ink-soft);
            margin: 0 0 28px;
            line-height: 1.6;
        }
        .rs-auth-form .form-group {
            margin-bottom: 18px;
        }
        .rs-auth-form label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--rs-ink-muted);
            margin-bottom: 7px;
        }
        .rs-auth-form .form-control,
        .rs-auth-form input[type="text"],
        .rs-auth-form input[type="email"],
        .rs-auth-form input[type="tel"],
        .rs-auth-form input[type="password"] {
            width: 100%;
            border: 1px solid var(--rs-cream-deep);
            background: var(--rs-cream);
            border-radius: 8px;
            padding: 13px 16px;
            font-family: 'Jost', sans-serif;
            font-size: 15px;
            color: var(--rs-ink);
            outline: none;
            transition: border-color .15s ease;
        }
        .rs-auth-form .form-control:focus,
        .rs-auth-form input:focus {
            border-color: var(--rs-gold);
            box-shadow: none;
        }
        .rs-auth-form .form-control.is-invalid {
            border-color: var(--rs-danger);
        }
        .rs-auth-form .invalid-feedback {
            display: block;
            font-size: 12px;
            color: var(--rs-danger);
            margin-top: 6px;
        }
        .rs-auth-form .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--rs-ink-muted);
            cursor: pointer;
        }
        .rs-auth-submit {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--rs-ink);
            color: #fff;
            font-size: 14px;
            letter-spacing: .16em;
            text-transform: uppercase;
            padding: 15px 28px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            transition: background .15s ease;
        }
        .rs-auth-submit:hover {
            background: var(--rs-gold-deep);
            color: #fff;
        }
        .rs-auth-submit:disabled {
            opacity: .55;
            cursor: not-allowed;
        }
        .rs-auth-link {
            color: var(--rs-gold-deep);
            text-decoration: underline;
            font-weight: 500;
        }
        .rs-auth-foot {
            font-size: 13px;
            color: var(--rs-ink-soft);
            margin-top: 22px;
        }
        .rs-auth-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--rs-ink);
            text-decoration: none;
            margin-top: 24px;
        }
        .rs-auth-social {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0 6px;
        }
        .rs-auth-social a {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--rs-cream);
            color: var(--rs-ink);
            text-decoration: none;
            border: 1px solid var(--rs-cream-deep);
        }
        .rs-auth-social a:hover {
            background: var(--rs-ink);
            color: #fff;
        }
        .rs-auth-divider {
            text-align: center;
            font-size: 12px;
            color: var(--rs-ink-faint);
            letter-spacing: .1em;
            text-transform: uppercase;
            margin: 22px 0 0;
        }
        .rs-auth-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: var(--rs-ink-soft);
            margin-bottom: 22px;
            cursor: pointer;
        }
        .rs-auth-checkbox input {
            margin-top: 3px;
        }
        @media (max-width: 860px) {
            .rs-auth-card {
                grid-template-columns: 1fr;
            }
            .rs-auth-visual {
                min-height: 220px;
            }
            .rs-auth-form-side {
                padding: 36px 28px;
            }
            .rs-auth-topbar {
                padding: 22px 20px;
            }
        }
    </style>

    @yield('css')
    <script>
        var AIZ = AIZ || {};
    </script>
</head>
<body class="aiz-rudraspirit">

    <div class="rs-auth-wrap">
        <div class="rs-auth-topbar">
            <a href="{{ route('home') }}" class="rs-auth-logo">
                @if (get_setting('header_logo') != null)
                    <img src="{{ uploaded_asset(get_setting('header_logo')) }}" alt="{{ get_setting('website_name') }}" class="rs-auth-logo-img">
                @else
                    <img src="{{ static_asset('assets/img/pages/rudraspirit/Logo_02-scaled.webp') }}" alt="{{ get_setting('website_name') }}" class="rs-auth-logo-img">
                @endif
            </a>
        </div>

        <div class="rs-auth-main">
            @php $rs_flash = session('flash_notification', collect())->toArray(); @endphp
            @if (count($rs_flash))
                <div class="rs-auth-flash">
                    @foreach ($rs_flash as $rs_msg)
                        <div class="rs-auth-alert rs-auth-alert-{{ $rs_msg['level'] ?? 'dark' }}" role="alert">
                            <i class="las {{ ($rs_msg['level'] ?? '') == 'success' ? 'la-check-circle' : 'la-exclamation-circle' }}"></i>
                            <span>{{ $rs_msg['message'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <!-- SCRIPTS -->
    @include('auth.login_register_js')

    @yield('script')

</body>
</html>
