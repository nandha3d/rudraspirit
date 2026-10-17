<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f080c">
    <title>{{ translate('Partner Portal Login') }} • {{ get_setting('website_name', 'Shiva Rudraksha Inc.') }}</title>
    
    <link rel="icon" href="{{ static_asset('images/brand/shiva-rudraksha-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #080a10;
            background-image: 
                radial-gradient(circle at 50% 25%, rgba(212, 175, 55, 0.15) 0%, transparent 60%),
                linear-gradient(180deg, rgba(8, 10, 18, 0.72) 0%, rgba(12, 8, 16, 0.65) 50%, rgba(6, 6, 10, 0.92) 100%),
                url("{{ static_asset('images/auth/spiritual-login-bg.webp') }}");
            background-size: cover;
            background-position: center 30%;
            background-attachment: fixed;
            color: #f7f3eb;
        }

        .spiritual-glass-card {
            width: min(440px, 100%);
            background: rgba(14, 11, 20, 0.75);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border: 1px solid rgba(212, 175, 55, 0.4);
            border-radius: 24px;
            padding: 40px 34px;
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.8),
                0 0 50px rgba(212, 175, 55, 0.12),
                inset 0 1px 1px rgba(255, 255, 255, 0.25);
            text-align: center;
        }

        .logo-wrap {
            position: relative;
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            display: grid;
            place-items: center;
        }

        .logo-glow {
            position: absolute;
            inset: -4px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.45) 0%, rgba(111, 15, 19, 0.25) 55%, transparent 75%);
            border-radius: 50%;
            filter: blur(10px);
        }

        .logo-wrap img {
            position: relative;
            z-index: 2;
            width: 56px;
            height: 56px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.6));
        }

        h1 {
            font-family: 'Cinzel', Georgia, serif;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #fae498;
            margin: 0 0 6px;
            text-transform: uppercase;
            text-shadow: 0 2px 14px rgba(212, 175, 55, 0.35);
        }

        p {
            color: #d1c7b7;
            font-size: 13px;
            margin: 0 0 24px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #e8d7b8;
            margin-bottom: 7px;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            height: 48px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(212, 175, 55, 0.32);
            border-radius: 12px;
            color: #ffffff;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.25s ease;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #fae498;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.35);
            outline: none;
        }

        .chk {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: #d5cbbb;
            margin: 12px 0 22px;
            cursor: pointer;
        }

        .chk input {
            width: 16px;
            height: 16px;
            accent-color: #d4af37;
            cursor: pointer;
        }

        button[type="submit"] {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #7c1218 0%, #9e1d23 45%, #630c10 100%);
            border: 1px solid rgba(250, 228, 152, 0.6);
            border-radius: 999px;
            color: #ffffff;
            font-family: 'Cinzel', Georgia, serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 10px 28px rgba(124, 18, 24, 0.55);
            transition: all 0.3s ease;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #8c151c 0%, #b22129 45%, #6f0e13 100%);
            box-shadow: 0 14px 34px rgba(124, 18, 24, 0.7), 0 0 20px rgba(212, 175, 55, 0.4);
        }

        .alert {
            background: rgba(111, 15, 19, 0.6);
            border: 1px solid rgba(250, 228, 152, 0.4);
            color: #ffe6ea;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: left;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            color: #fae498;
            font-size: 12.5px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form class="spiritual-glass-card" method="POST" action="{{ route('partner.login.post') }}">
        @csrf
        <div class="logo-wrap">
            <div class="logo-glow"></div>
            <img src="{{ static_asset('images/brand/shiva-rudraksha-logo.png') }}" alt="Shiva Rudraksha">
        </div>
        <h1>{{ translate('Partner Portal') }}</h1>
        <p>{{ translate('Sign in to view your sacred profit share.') }}</p>
        
        @foreach (session('flash_notification', collect())->toArray() as $message)
            <div class="alert">{{ $message['message'] }}</div>
        @endforeach

        <div class="form-group">
            <label>{{ translate('Email Address') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="johndoe@example.com" required autofocus>
        </div>

        <div class="form-group">
            <label>{{ translate('Password') }}</label>
            <input type="password" name="password" placeholder="••••••••••••" required>
        </div>

        <label class="chk">
            <input type="checkbox" name="remember">
            <span>{{ translate('Remember me') }}</span>
        </label>

        <button type="submit">{{ translate('Sign In to Portal') }}</button>

        <div>
            <a href="/" class="back-link">← {{ translate('Back to Storefront') }}</a>
        </div>
    </form>
</body>
</html>
