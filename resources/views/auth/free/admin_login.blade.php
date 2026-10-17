<!-- Shiva Rudraksha Inc. - Spiritual Glassmorphism Admin Login -->
<div class="spiritual-auth-page">
    <div class="spiritual-glass-card">
        <div class="row no-gutters">
            <!-- Left Side Sacred Altar Art -->
            <div class="col-lg-6 spiritual-left-panel d-none d-lg-flex">
                <img 
                    src="{{ uploaded_asset(get_setting('admin_login_page_image')) ?: static_asset('images/auth/spiritual-card-art.webp') }}" 
                    alt="Sacred Nepal Rudraksha & Himalayan Altar" 
                    class="spiritual-panel-img"
                    onerror="this.src='{{ static_asset('images/auth/spiritual-card-art.jpg') }}'"
                >
                <div class="spiritual-left-overlay"></div>
                <div class="spiritual-left-content">
                    <span class="spiritual-mantra-badge">🕉 ॐ नमः शिवाय</span>
                    <h2>Sacred Himalayan Consecration</h2>
                    <p>Access the Shiva Rudraksha administrative portal. Sourced directly from holy groves in Nepal with lab certification and Prana Pratishtha energization.</p>
                    <div class="spiritual-badges-row">
                        <span class="spiritual-pill">✦ 100% Nepal Origin</span>
                        <span class="spiritual-pill">✦ Govt. Lab Certified</span>
                        <span class="spiritual-pill">✦ Vedic Energized</span>
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
                    <h1>{{ translate('Welcome to') }} {{ env('APP_NAME', 'Shiva Rudraksha') }}</h1>
                    <p>{{ translate('Sign in to your sacred account') }}</p>
                </div>

                <!-- Form -->
                <form class="form-default spiritual-form" id="login-form" role="form" action="{{ route('login') }}" method="POST">
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

                    <!-- Recaptcha Error Alert -->
                    @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_admin_login') == 1)
                        @if ($errors->has('g-recaptcha-response'))
                            <div class="alert alert-danger mb-3" role="alert">
                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </div>
                        @endif
                    @endif

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

                <!-- DEMO MODE -->
                @if (env("DEMO_MODE") == "On")
                    <div class="mt-4 p-3" style="background: rgba(255,255,255,0.06); border-radius: 12px; border: 1px dashed rgba(212,175,55,0.4);">
                        <div class="d-flex justify-content-between align-items-center text-white small">
                            <span>admin@example.com / 123456</span>
                            <button type="button" class="btn btn-warning btn-xs" onclick="autoFillAdmin()">{{ translate('Copy') }}</button>
                        </div>
                    </div>
                @endif

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
