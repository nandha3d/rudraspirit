@extends('auth.layouts.rudraspirit')

@section('content')
<div class="rs-auth-card">
    <div class="rs-auth-visual">
        <img src="{{ uploaded_asset(get_setting('customer_login_page_image')) }}" alt="{{ translate('Customer Login Page Image') }}">
    </div>

    <div class="rs-auth-form-side">
        <div class="rs-auth-kicker">{{ translate('Welcome Back') }}</div>
        <h1 class="rs-auth-title">{{ translate('Login to Your Account') }}</h1>
        <p class="rs-auth-sub">{{ translate('Sign in to continue your Rudraksha journey.') }}</p>

        <form class="rs-auth-form loginForm" id="user-login-form" role="form" action="{{ route('login') }}" method="POST">
            @csrf

            @if (addon_is_activated('otp_system'))
                <div class="form-group phone-form-group mb-1">
                    <label for="phone">{{ translate('Phone') }}</label>
                    <input type="tel" phone-number id="phone-code" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                </div>

                <input type="hidden" name="country_code" value="">

                <div class="form-group email-form-group mb-1 d-none">
                    <label for="email">{{ translate('Email') }}</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('johndoe@example.com') }}" name="email" id="email" autocomplete="off">
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                </div>

                <div class="form-group text-right">
                    <button class="btn btn-link p-0 rs-auth-link fs-12 fw-400" type="button" onclick="toggleEmailPhone(this)" style="text-decoration:none;"><i>*{{ translate('Use Email Instead') }}</i></button>
                </div>
            @else
                <div class="form-group">
                    <label for="email">{{ translate('Email') }}</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('johndoe@example.com') }}" name="email" id="email" autocomplete="off">
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                </div>
            @endif

            <div class="password-login-block">
                <div class="form-group">
                    <label for="password">{{ translate('Password') }}</label>
                    <div class="position-relative">
                        <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('Password') }}" name="password" id="password">
                        <i class="password-toggle las la-2x la-eye"></i>
                    </div>
                </div>

                @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_login') == 1)
                    @if ($errors->has('g-recaptcha-response'))
                        <span class="border invalid-feedback rounded p-2 mb-3 bg-danger text-white" role="alert" style="display: block;"><strong>{{ $errors->first('g-recaptcha-response') }}</strong></span>
                    @endif
                @endif

                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:13px;">
                    <label class="aiz-checkbox mb-0" style="color:var(--rs-ink-soft);">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="has-transition fs-12 fw-400">{{ translate('Remember Me') }}</span>
                        <span class="aiz-square-check"></span>
                    </label>
                    <div>
                        @if(get_setting('login_with_otp'))
                            <a href="javascript:void(0);" class="rs-auth-link fs-12 toggle-login-with-otp" onclick="toggleLoginPassOTP(this)" style="text-decoration:none;">{{ translate('Login With OTP') }} / </a>
                        @endif
                        <a href="{{ route('password.request') }}" class="rs-auth-link fs-12">{{ translate('Forgot password?') }}</a>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="rs-auth-submit submit-button">{{ translate('Login') }}</button>
            </div>
        </form>

        @if (env("DEMO_MODE") == "On")
            <div class="mt-3">
                <button class="btn btn-sm rs-btn-outline" type="button" onclick="autoFillCustomer()" style="border-radius:6px;">{{ translate('Copy demo credentials') }}</button>
            </div>
        @endif

        @if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1 || get_setting('apple_login') == 1)
            <div class="rs-auth-divider">{{ translate('Or Login With') }}</div>
            <div class="rs-auth-social">
                @if (get_setting('facebook_login') == 1)
                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}"><i class="lab la-facebook-f"></i></a>
                @endif
                @if (get_setting('twitter_login') == 1)
                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg>
                    </a>
                @endif
                @if(get_setting('google_login') == 1)
                    <a href="{{ route('social.login', ['provider' => 'google']) }}"><i class="lab la-google"></i></a>
                @endif
                @if (get_setting('apple_login') == 1)
                    <a href="{{ route('social.login', ['provider' => 'apple']) }}"><i class="lab la-apple"></i></a>
                @endif
            </div>
        @endif

        <p class="rs-auth-foot">
            {{ translate('Dont have an account?') }}
            <a href="{{ route('user.registration') }}" class="rs-auth-link">{{ translate('Register Now') }}</a>
        </p>

        <a href="{{ url()->previous() }}" class="rs-auth-back">
            <i class="las la-arrow-left fs-16"></i> {{ translate('Back to Previous Page') }}
        </a>
    </div>
</div>
@endsection

@section('script')
<script>
    function autoFillCustomer(){
        $('#email').val('customer@example.com');
        $('#password').val('123456');
    }
</script>

@if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_login') == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('CAPTCHA_KEY') }}"></script>
    <script type="text/javascript">
        document.getElementById('user-login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute(`{{ env('CAPTCHA_KEY') }}`, {action: 'register'}).then(function(token) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', 'g-recaptcha-response');
                    input.setAttribute('value', token);
                    e.target.appendChild(input);

                    var actionInput = document.createElement('input');
                    actionInput.setAttribute('type', 'hidden');
                    actionInput.setAttribute('name', 'recaptcha_action');
                    actionInput.setAttribute('value', 'recaptcha_customer_login');
                    e.target.appendChild(actionInput);

                    e.target.submit();
                });
            });
        });
    </script>
@endif
@endsection
