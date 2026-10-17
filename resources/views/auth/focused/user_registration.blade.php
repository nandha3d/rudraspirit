@extends('auth.layouts.authentication')

@section('content')
<!-- Shiva Rudraksha Inc. - Spiritual Glassmorphism User Registration -->
<div class="spiritual-auth-page user-page">
    <div class="spiritual-glass-card">
        <div class="row no-gutters">
            <!-- Left Side Sacred Altar Art (SAME SACRED IMAGE AS LOGIN) -->
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
                    <h2>Sacred Consecrated Journey</h2>
                    <p>Begin your divine connection with authentic Nepal Rudraksha. Every bead is lab certified with X-ray authenticity, sanctified in holy groves, and energised by Vedic priests.</p>
                    <div class="spiritual-badges-row">
                        <span class="spiritual-pill">✦ 100% Nepal Origin</span>
                        <span class="spiritual-pill">✦ Govt. Lab Certified</span>
                        <span class="spiritual-pill">✦ Prana Pratishtha</span>
                    </div>
                </div>
            </div>

            <!-- Right Side Glassmorphic Form -->
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
                        <h1>{{ translate('Create an Account') }}</h1>
                        <p>{{ translate('Begin your sacred Rudraksha journey') }}</p>
                    </div>

                    <!-- Form -->
                    <form id="reg-form" class="form-default spiritual-form" role="form" action="{{ route('register') }}" method="POST">
                        @csrf

                        <!-- Full Name -->
                        <div class="form-group">
                            <label for="name">{{ translate('Full Name') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="text" 
                                    class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" 
                                    value="{{ old('name') }}" 
                                    placeholder="{{ translate('Enter your full name') }}" 
                                    name="name"
                                    id="name"
                                    required
                                >
                            </div>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback d-block mt-1 text-danger" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="email">{{ translate('Email Address') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="email" 
                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                    value="{{ old('email') }}" 
                                    placeholder="{{ translate('devotee@example.com') }}" 
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

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">{{ translate('Password') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="password" 
                                    class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                    placeholder="{{ translate('At least 6 characters') }}" 
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

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="password_confirmation">{{ translate('Confirm Password') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    placeholder="{{ translate('Confirm your password') }}" 
                                    name="password_confirmation" 
                                    id="password_confirmation"
                                    required
                                >
                                <i class="password-toggle las la-eye" onclick="togglePasswordVisibility('password_confirmation', this)"></i>
                            </div>
                        </div>

                        <!-- Recaptcha -->
                        @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1)
                            @if ($errors->has('g-recaptcha-response'))
                                <div class="alert alert-danger mb-2 small" role="alert">
                                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                </div>
                            @endif
                        @endif

                        <!-- Terms Checkbox -->
                        <div class="spiritual-meta-row" style="margin: 8px 0 16px;">
                            <label class="spiritual-checkbox" style="align-items: flex-start; text-align: left;">
                                <input type="checkbox" name="checkbox_example_1" required style="margin-top: 3px;">
                                <span>{{ translate('I agree to the') }} <a href="{{ route('terms') }}" class="spiritual-forgot-link" target="_blank">{{ translate('Terms & Conditions') }}</a></span>
                            </label>
                        </div>

                        <!-- Cute Compact Centered Submit Button -->
                        <div class="spiritual-btn-wrap">
                            <button type="submit" class="spiritual-btn-submit" id="createAccountBtn">
                                <span>{{ translate('Create Account') }}</span>
                                <i class="las la-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Social Login -->
                    @if (get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('apple_login') == 1)
                        <div class="text-center my-3 text-muted small" style="color: #d1c7b7 !important;">
                            <span>— {{ translate('or join with') }} —</span>
                        </div>
                        <div class="d-flex justify-content-center gap-2" style="gap: 12px;">
                            @if (get_setting('google_login') == 1)
                                <a href="{{ route('social.login', ['provider' => 'google']) }}" class="btn btn-outline-light btn-sm px-3 py-2" style="border-radius: 8px; border-color: rgba(212,175,55,0.4); color: #fae498;">
                                    <i class="lab la-google mr-1"></i> Google
                                </a>
                            @endif
                            @if (get_setting('facebook_login') == 1)
                                <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="btn btn-outline-light btn-sm px-3 py-2" style="border-radius: 8px; border-color: rgba(212,175,55,0.4); color: #fae498;">
                                    <i class="lab la-facebook-f mr-1"></i> Facebook
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Already have an account prompt -->
                    <div class="spiritual-alt-action">
                        {{ translate('Already have an account?') }} 
                        <a href="{{ route('user.login') }}">{{ translate('Sign In') }}</a>
                    </div>

                    <!-- Go Back -->
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="spiritual-back-link">
                            <i class="las la-arrow-left"></i>
                            <span>{{ translate('Back to Storefront') }}</span>
                        </a>
                    </div>
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
