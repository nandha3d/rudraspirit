@extends('auth.layouts.rudraspirit')

@section('content')
<div class="rs-auth-card">
    <div class="rs-auth-visual">
        <img src="{{ uploaded_asset(get_setting('password_reset_page_image')) }}" alt="{{ translate('Password Reset Page Image') }}">
    </div>

    <div class="rs-auth-form-side">
        <div class="rs-auth-kicker">{{ translate('Account Recovery') }}</div>
        <h1 class="rs-auth-title">{{ translate('Reset Password') }}</h1>
        <p class="rs-auth-sub">{{ translate('Enter your email address and new password and confirm password.') }}</p>

        <form class="rs-auth-form" role="form" action="{{ route('password.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">{{ translate('Email') }}</label>
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" @if(!empty($email ?? null)) readonly @endif placeholder="{{ translate('Email') }}" required autofocus>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <label for="code">{{ translate('Code') }}</label>
                <input id="code" type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{old('code') }}" placeholder="{{translate('Code')}}" required autofocus>
                @if ($errors->has('code'))
                    <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('code') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <label for="password">{{ translate('New Password') }}</label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ translate('New Password') }}" required>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('password') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <label for="password-confirm">{{ translate('Confirm Password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ translate('Confirm Password') }}" required>
            </div>

            <div class="mt-4">
                <button type="submit" class="rs-auth-submit">{{ translate('Reset Password') }}</button>
            </div>
        </form>

        <a href="{{ url()->previous() }}" class="rs-auth-back">
            <i class="las la-arrow-left fs-16"></i> {{ translate('Back to Previous Page') }}
        </a>
    </div>
</div>
@endsection
