@extends('auth.layouts.authentication')

@section('content')
<!-- Shiva Rudraksha Inc. - Spiritual Glassmorphism User Registration -->
<div class="spiritual-auth-page user-page">
    <div class="spiritual-glass-card">
        <div class="row no-gutters">
            <!-- Left Side Sacred Altar Art with Guaranteed Generous Padding -->
            <div class="col-lg-6 spiritual-left-panel d-none d-lg-flex" style="padding: 0 !important;">
                <img 
                    src="{{ static_asset('images/auth/spiritual-card-art.webp') }}" 
                    alt="Sacred Nepal Rudraksha & Himalayan Consecration" 
                    class="spiritual-panel-img"
                    onerror="this.src='{{ static_asset('images/auth/spiritual-card-art.jpg') }}'"
                >
                <div class="spiritual-left-overlay"></div>
                <div class="spiritual-left-content" style="padding: 48px 36px 44px 44px !important;">
                    <span class="spiritual-mantra-badge">🕉 ॐ नमः शिवाय</span>
                    <h2>Sacred Devotee Journey</h2>
                    <p>Create your personal account with Shiva Rudraksha. Enjoy priority access to rare Mukhis, Vedic astrologer recommendations, and lifetime certification tracking.</p>
                    <div class="spiritual-badges-row">
                        <span class="spiritual-pill">✦ Lifetime Authenticity</span>
                        <span class="spiritual-pill">✦ Govt. Lab Certified</span>
                        <span class="spiritual-pill">✦ Vedic Energized</span>
                    </div>
                </div>
            </div>

            <!-- Right Side Glassmorphic Form (Properly Padded & Narrowed Box) -->
            <div class="col-lg-6 spiritual-right-panel" style="padding: 48px 40px 44px 40px !important;">
                <div class="spiritual-form-box" style="width: 100% !important; max-width: 310px !important; margin: 0 auto !important;">
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
                        <p>{{ translate('Begin your sacred spiritual journey') }}</p>
                    </div>

                    <!-- Registration Form -->
                    <form class="form-default spiritual-form" id="reg-form" role="form" action="{{ route('register') }}" method="POST">
                        @csrf

                        <!-- Full Name Field -->
                        <div class="form-group">
                            <label for="name">{{ translate('Full Name') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="text" 
                                    class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" 
                                    value="{{ old('name') }}" 
                                    placeholder="{{ translate('Your full name') }}" 
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

                        <!-- Email or Phone -->
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

                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password">{{ translate('Password') }}</label>
                            <div class="spiritual-input-wrap">
                                <input 
                                    type="password" 
                                    class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                    placeholder="{{ translate('Create password (min. 6 chars)') }}" 
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

                        <!-- Confirm Password Field -->
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

                        <!-- Terms and Conditions Checkbox -->
                        <div class="spiritual-meta-row">
                            <label class="spiritual-checkbox">
                                <input type="checkbox" name="checkbox_example_1" required>
                                <span>{{ translate('I agree to the') }} <a href="{{ route('terms') }}" target="_blank" class="spiritual-forgot-link">{{ translate('Terms & Conditions') }}</a></span>
                            </label>
                        </div>

                        <!-- Cute Compact Pill Submit Button -->
                        <div class="spiritual-btn-wrap">
                            <button type="submit" class="spiritual-btn-submit" id="createAccountBtn">
                                <span>{{ translate('Create Account') }}</span>
                                <i class="las la-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Already have an account prompt -->
                    <div class="spiritual-alt-action">
                        <span>{{ translate('Already have an account?') }}</span>
                        <a href="{{ route('user.login') }}">{{ translate('Log In') }}</a>
                    </div>

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
