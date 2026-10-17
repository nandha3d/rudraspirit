<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f080c">
    <title>{{ translate('Partner Portal Login') }} • {{ env('APP_NAME', 'shivarudrakshainc') }}</title>
    
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
                radial-gradient(circle at 50% 25%, rgba(212, 175, 55, 0.18) 0%, transparent 60%),
                linear-gradient(180deg, rgba(8, 10, 18, 0.72) 0%, rgba(12, 8, 16, 0.65) 50%, rgba(6, 6, 10, 0.92) 100%),
                url("{{ static_asset('images/auth/spiritual-login-bg.webp') }}");
            background-size: cover;
            background-position: center 30%;
            background-attachment: fixed;
            color: #f7f3eb;
        }

        .spiritual-glass-card {
            width: min(460px, 100%);
            background: rgba(14, 11, 20, 0.76);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border: 1px solid rgba(212, 175, 55, 0.42);
            border-radius: 24px;
            padding: 44px 38px;
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.85),
                0 0 50px rgba(212, 175, 55, 0.14),
                inset 0 1px 1px rgba(255, 255, 255, 0.28);
            text-align: center;
        }

        .logo-wrap {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            display: grid;
            place-items: center;
        }

        .logo-glow {
            position: absolute;
            inset: -6px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.5) 0%, rgba(111, 15, 19, 0.28) 55%, transparent 75%);
            border-radius: 50%;
            filter: blur(12px);
        }

        .logo-wrap img {
            position: relative;
            z-index: 2;
            width: 64px;
            height: 64px;
            object-fit: contain;
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.7));
        }

        .mantra-badge {
            display: inline-block;
            padding: 5px 14px;
            background: rgba(212, 175, 55, 0.16);
            border: 1px solid rgba(212, 175, 55, 0.4);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #fae498;
            margin-bottom: 12px;
            text-transform: uppercase;
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

        .input-wrap {
            position: relative;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            height: 48px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 12px;
            color: #fff;
            padding: 10px 16px;
            font-size: 14px;
            outline: none;
            transition: all 0.25s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #fae498;
            box-shadow: 0 0 16px rgba(212, 175, 55, 0.3);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #fae498;
            cursor: pointer;
            font-size: 18px;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 14px 0 22px;
            font-size: 13px;
            color: #d1c7b7;
        }

        .remember-row label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: none;
            letter-spacing: normal;
            font-weight: normal;
            cursor: pointer;
            margin: 0;
        }

        .remember-row input[type="checkbox"] {
            accent-color: #d4af37;
        }

        button.btn-submit {
            position: relative;
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #7c1218 0%, #9e1a22 50%, #5d0c10 100%);
            border: 1px solid rgba(212, 175, 55, 0.65);
            border-radius: 12px;
            color: #fff;
            font-family: 'Cinzel', Georgia, serif;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 10px 28px rgba(124, 18, 24, 0.55), inset 0 1px 1px rgba(255, 255, 255, 0.35);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button.btn-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #8c151c 0%, #b22129 45%, #6f0e13 100%);
            border-color: #fae498;
            box-shadow: 0 14px 34px rgba(124, 18, 24, 0.7), 0 0 20px rgba(212, 175, 55, 0.4);
        }

        .alert-error {
            background: rgba(180, 20, 20, 0.25);
            border: 1px solid rgba(255, 80, 80, 0.5);
            color: #ffb4b4;
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
            margin-top: 22px;
            color: #fae498;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .back-link:hover {
            color: #fff;
            transform: translateX(-4px);
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.6);
        }
    </style>
</head>
<body>
    <div class="spiritual-glass-card">
        <div class="logo-wrap">
            <div class="logo-glow"></div>
            <img 
                src="{{ static_asset('images/brand/shiva-rudraksha-logo.png') }}" 
                alt="Shiva Rudraksha Inc."
                onerror="this.src='{{ static_asset('images/brand/shiva-rudraksha-logo.png') }}'"
            >
        </div>

        <span class="mantra-badge">🕉 ॐ नमः शिवाय</span>
        <h1>{{ translate('Partner Portal') }}</h1>
        <p>{{ translate('Sign in to access your profit-share dashboard') }}</p>

        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('partner.login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">{{ translate('Partner Email') }}</label>
                <div class="input-wrap">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ translate('partner@example.com') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">{{ translate('Password') }}</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password" placeholder="{{ translate('Enter your password') }}" required>
                    <i class="password-toggle las la-eye" onclick="togglePasswordVisibility('password', this)"></i>
                </div>
            </div>

            <div class="remember-row">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>{{ translate('Remember Me') }}</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">
                <span>{{ translate('Sign In to Portal') }}</span>
                <i class="las la-arrow-right"></i>
            </button>
        </form>

        <a href="{{ route('home') }}" class="back-link">
            <i class="las la-arrow-left"></i>
            <span>{{ translate('Back to Storefront') }}</span>
        </a>
    </div>

    <script>
    function togglePasswordVisibility(id, el) {
        var input = document.getElementById(id);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            el.classList.remove('la-eye');
            el.classList.add('la-eye-slash');
        } else {
            input.type = 'password';
            el.classList.remove('la-eye-slash');
            el.classList.add('la-eye');
        }
    }
    </script>
</body>
</html>
