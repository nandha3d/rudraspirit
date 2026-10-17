@extends('auth.layouts.authentication')

@section('content')
<!-- Shiva Rudraksha Inc. - Spiritual Glassmorphism User Login -->
<div class="spiritual-auth-page">
    <div class="spiritual-glass-card">
        <div class="row no-gutters">
            <!-- Left Side Sacred Altar Art -->
            <div class="col-lg-6 spiritual-left-panel d-none d-lg-flex">
                <img 
                    src="{{ uploaded_asset(get_setting('customer_login_page_image')) ?: static_asset('images/auth/spiritual-card-art.webp') }}" 
                    alt="Sacred Nepal Rudraksha & Himalayan Altar" 
                    class="spiritual-panel-img"
                    onerror="this.src='{{ static_asset('images/auth/spiritual-card-art.jpg') }}'"
                >
                <div class="spiritual-left-overlay"></div>
                <div class="spiritual-left-content">
                    <span class="spiritual-mantra-badge">🕉 ॐ नमः शिवाय</span>
                    <h2>Divine Blessings Await</h2>
                    <p>Welcome back to your sacred Shiva Rudraksha portal. Track your energized combinations, custom malas, and Vedic Kundali consultations.</p>
                    <div class="spiritual-badges-row">
                        <span class="spiritual-pill">✦ Authentic Certified Beads</span>
                        <span class="spiritual-pill">✦ Lifetime Authenticity</span>
                        <span class="spiritual-pill">✦ Fast Worldwide Delivery</span>
                    </div>
                </div>
            </div>

            <!-- Right Side Glassmorphic Form -->
            <div class="col-lg-6 spiritual-right-panel">
                <!-- Brand Crest Header -->
                <div class="spiritual-brand-header">
                    <div class="spiritual-logo-wrapper">
                        <div class="spiritual-logo-glow"></div>
                        <img 
                            src="{{ uploaded_asset(get_setting('site_icon')) ?: static_asset('images/brand/shiva-rudraksha-logo.png') }}" 
                            alt="Shiva Rudraksha Inc."
                            onerror="this.src='{{ static_asset('images/brand/shiva-rudraksha-logo.png') }}'"
                        >
                    </div>
                    <h1>{{ translate('Welcome Back') }}</h1>
                    <p>{{ translate('Sign in to your devotee account') }}</p>
                </div>

                <!-- Form -->
                <form class="form-default loginForm spiritual-form" id="user-login-form" role="form" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email">{{ translate('Email Address') }}</label>
                        <div class="spiritual-input-wrap">
                            <input 
                                type="email" 
                                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                value="{{ old('email') }}" 
                                placeholder="{{ translate('johndoe@example.com') }}" 
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
                            <i class="password-toggle las la-eye"></i>
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

                    <!-- Submit Button -->
                    <button type="submit" class="spiritual-btn-submit">
                        <span>{{ translate('Login') }}</span>
                        <i class="las la-arrow-right"></i>
                    </button>
                </form>

                <!-- Go Back -->
                <div class="text-center">
                    <a href="{{ url()->previous() }}" class="spiritual-back-link">
                        <i class="las la-arrow-left"></i>
                        <span>{{ translate('Back to Previous Page') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
