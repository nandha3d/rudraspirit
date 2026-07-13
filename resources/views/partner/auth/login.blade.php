<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ translate('Partner Login') }} — {{ get_setting('website_name') }}</title>
    <style>
        *{box-sizing:border-box} body{font-family:system-ui,'Segoe UI',Roboto,sans-serif;background:#12100c;color:#eee;margin:0;height:100vh;display:flex;align-items:center;justify-content:center}
        .card{background:#1c1811;border:1px solid #2e2718;border-radius:16px;padding:34px;width:360px;box-shadow:0 30px 70px rgba(0,0,0,.4)}
        h1{font-size:19px;margin:0 0 4px;color:#e7c98a} p{color:#9a927f;font-size:13px;margin:0 0 22px}
        label{font-size:12px;color:#9a927f;display:block;margin:12px 0 5px}
        input{width:100%;padding:12px 14px;border-radius:9px;border:1px solid #3a3220;background:#12100c;color:#eee;font-size:14px}
        button{width:100%;margin-top:18px;padding:13px;border:none;border-radius:9px;background:#b4894a;color:#12100c;font-weight:700;cursor:pointer;font-size:14px}
        button:hover{background:#c99a55}
        .alert{background:#3a1d24;border:1px solid #6b2b38;color:#ff9aa8;padding:10px 12px;border-radius:8px;font-size:13px;margin-bottom:14px}
        .chk{display:flex;align-items:center;gap:8px;font-size:13px;color:#9a927f;margin-top:12px}
        .chk input{width:auto}
    </style>
</head>
<body>
    <form class="card" method="POST" action="{{ route('partner.login.post') }}">
        @csrf
        <h1>{{ translate('Partner Portal') }}</h1>
        <p>{{ translate('Sign in to view your profit share.') }}</p>
        @foreach (session('flash_notification', collect())->toArray() as $message)
            <div class="alert">{{ $message['message'] }}</div>
        @endforeach
        <label>{{ translate('Email') }}</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        <label>{{ translate('Password') }}</label>
        <input type="password" name="password" required>
        <label class="chk"><input type="checkbox" name="remember"> {{ translate('Remember me') }}</label>
        <button type="submit">{{ translate('Sign in') }}</button>
    </form>
</body>
</html>
