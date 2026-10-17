@extends('auth.layouts.authentication')

@section('content')
<!-- Shiva Rudraksha Inc. - Spiritual Glassmorphism User Login -->
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
                    <h2>Divine Blessings Await</h2>
                    <p>Welcome back to your sacred Shiva Rudraksha portal. Access your energized bead orders, custom malas, and Vedic Kundali consultations.</p>
                    <div class="spiritual-badges-row">
                        <span class="spiritual-pill">✦ Authentic Certified Beads</span>
                        <span class="spiritual-pill">✦ Lifetime Authenticity</span>
                        <span class="spiritual-pill">✦ Fast Worldwide Delivery</span>
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
                        <h1>{{ translate('Welcome Back') }}</h1>
                        <p>{{ translate('Sign in to your devotee account') }}</p>
                    </div>

                    <!-- Form -->
                    <form class="form-default loginForm spiritual-form" id="user-login-form" role="form" action="{{ route('login') }}" method="POST">
                        @csrf

                        @if (addon_is_activated('otp_system'))
                            <div class="form-group phone-form-group mb-2">
                                <label for="phone">{{ translate('Phone Number') }}</label>
                                <div class="spiritual-input-wrap">
                                    <input type="tel" id="phone-code" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') }}" placeholder="{{ translate('+91 98765 43210') }}" name="phone" autocomplete="off">
                                </div>
                            </div>

                            <input type="hidden" name="country_code" value="">

                            <div class="form-group email-form-group mb-2 d-none">
                                <label for="email">{{ translate('Email Address') }}</label>
                                <div class="spiritual-input-wrap">
                                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('devotee@example.com') }}" name="email" id="email" autocomplete="off">
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback d-block mt-1 text-danger" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                                @endif
                            </div>

                            <div class="text-right mb-2">
                                <button class="btn btn-link p-0 fs-12 text-warning" type="button" onclick="toggleEmailPhone(this)" style="text-decoration:none; color: #fae498 !important;"><i>*{{ translate('Use Email Instead') }}</i></button>
                            </div>
                        @else
                            <!-- Email Field -->
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
                        @endif

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

                        <!-- Cute Compact Centered Submit Button -->
                        <div class="spiritual-btn-wrap">
                            <button type="submit" class="spiritual-btn-submit">
                                <span>{{ translate('Login to Account') }}</span>
                                <i class="las la-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Social Login -->
                    @if (get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('apple_login') == 1)
                        <div class="text-center my-3 text-muted small" style="color: #d1c7b7 !important;">
                            <span>— {{ translate('or sign in with') }} —</span>
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

                    <!-- Register Prompt -->
                    <div class="spiritual-alt-action">
                        {{ translate("Don't have an account?") }} 
                        <a href="{{ route('user.registration') }}">{{ translate('Register Free') }}</a>
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
