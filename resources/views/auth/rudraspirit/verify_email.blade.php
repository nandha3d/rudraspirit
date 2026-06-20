@extends('auth.layouts.rudraspirit')

@section('content')
<div class="rs-auth-card">
    <div class="rs-auth-visual">
        <img src="{{ uploaded_asset(get_setting('password_reset_page_image')) }}" alt="{{ translate('Password Reset Page Image') }}">
    </div>

    <div class="rs-auth-form-side">
        <div class="rs-auth-kicker">{{ translate('Almost There') }}</div>
        <h1 class="rs-auth-title">{{ translate('Verify Your Email Address') }}</h1>
        <p class="rs-auth-sub">{{ translate('Before proceeding, please check your email for a verification link. If you did not receive the email.') }}</p>

        <a href="{{ route('verification.resend') }}" class="rs-auth-submit" style="text-decoration:none;display:inline-flex;">{{ translate('Click here to request another') }}</a>
        @if (session('resent'))
            <div class="alert alert-success mt-3 mb-0" role="alert">
                {{ translate('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif

        <a href="{{ url()->previous() }}" class="rs-auth-back">
            <i class="las la-arrow-left fs-16"></i> {{ translate('Back to Previous Page') }}
        </a>
    </div>
</div>
@endsection
