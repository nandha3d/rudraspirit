@extends('auth.layouts.authentication')

@section('content')
<!-- Shiva Rudraksha Inc. - Spiritual Glassmorphism Seller Login -->
<div class="spiritual-auth-page">
    <div class="spiritual-glass-card">
        <div class="row no-gutters">
            <!-- Left Side Sacred Altar Art -->
            <div class="col-lg-6 spiritual-left-panel d-none d-lg-flex">
                <img 
                    src="{{ static_asset('images/auth/spiritual-card-art.webp') }}" 
                    alt="Sacred Nepal Rudraksha & Himalayan Consecration" 
                    class="spiritual-panel-img"
                    onerror="this.src='{{ static_asset('images/auth/spiritual-card-art.jpg') }}'"
                >
                <div class="spiritual-left-overlay"></div>
                <div class="spiritual-left-content">
                    <span class="spiritual-mantra-badge">🕉 ॐ नमः शिवाय</span>
                    <h2>Sacred Artisan Sanctuary</h2>
                    <p>Access the Shiva Rudraksha verified merchant portal. Manage consecrated inventory, artisan certifications, and global temple dispatches.</p>
                    <div class="spiritual-badges-row">
                        <span class="spiritual-pill">✦ Verified Merchant</span>
                        <span class="spiritual-pill">✦ Direct Temple Sourcing</span>
                        <span class="spiritual-pill">✦ Fair Value Pricing</span>
                    </div>
                </div>
            </div>

            <!-- Right Side Glassmorphic Form (Properly Padded & Narrowed Box) -->
            <div class="col-lg-6 spiritual-right-panel">
                <div class="spiritual-form-box">
                    <!-- Brand Crest Header with larger logo -->
                    <div class="spiritual-brand-header">
                        <div class="spiritual-logo-wrapper">
                            <div class="spiritual-logo-glow"></div>
                            <img 
                                src="{{ static_asset('images/brand/shiva-rudraksha-logo.png') }}" 
                                alt="Shiva Rudraksha Inc."
                                onerror="this.src='{{ static_asset('images/brand/shiva-rudraksha-logo.png') }}'"
                            >
                        </div>
                        <h1>{{ translate('Artisan & Seller Portal') }}</h1>
                        <p>{{ translate('Sign in to your merchant dashboard') }}</p>
                    </div>

                    <!-- Form -->
                    <form class="form-default spiritual-form" id="seller-login-form" role="form" action="{{ route('login') }}" method="POST">
                        @csrf

                        <!-- Email Field -->
                        <div class="form-group">
                            <label for="email">{{ translate('Email Address') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="email" 
                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                    value="{{ old('email') }}" 
                                    placeholder="{{ translate('seller@example.com') }}" 
                                    name="email" 
                                    id="email" 
                                    autocomplete="off"
                                    required
                                >
                            </div>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback d-block mt-1 text-danger" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password">{{ translate('Password') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="password" 
                                    class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                    placeholder="{{ translate('Enter your password') }}" 
                                    name="password" 
                                    id="password"
                                    required
                                >
                                <i class="password-toggle las la-eye" onclick="togglePasswordVisibility('password', this)"></i>
                            </div>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback d-block mt-1 text-danger" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <!-- Remember Me & Forgot Password Row -->
                        <div class="spiritual-meta-row">
                            <label class="spiritual-checkbox">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>{{ translate('Remember Me') }}</span>
                            </label>

                            <a href="{{ route('password.request') }}" class="spiritual-forgot-link">
                                {{ translate('Forgot password?') }}
                            </a>
                        </div>

                        <!-- Cute Compact Pill Submit Button -->
                        <div class="spiritual-btn-wrap">
                            <button type="submit" class="spiritual-btn-submit">
                                <span>{{ translate('Login to Dashboard') }}</span>
                                <i class="las la-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Go Back Link -->
                    <a href="{{ route('home') }}" class="spiritual-back-link">
                        <i class="las la-arrow-left"></i>
                        <span>{{ translate('Back to Storefront') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
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
@endsection
