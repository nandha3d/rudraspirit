@extends('auth.layouts.rudraspirit')

@section('content')
<div class="rs-auth-card">
    <div class="rs-auth-visual">
        <img src="{{ get_setting('customer_register_page_image') ? uploaded_asset(get_setting('customer_register_page_image')) : static_asset('assets/img/pages/rudraspirit/auth-essence.webp') }}"
             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/pages/rudraspirit/Gemini_Generated_Image_9keguo9keguo9keg.webp') }}';"
             alt="{{ translate('Customer Register Page Image') }}">
    </div>

    <div class="rs-auth-form-side">
        <div class="rs-auth-kicker">{{ translate('Join Us') }}</div>
        <h1 class="rs-auth-title">{{ translate('Create an Account') }}</h1>
        <p class="rs-auth-sub">{{ translate('Begin your Rudraksha journey with hand-picked, certified beads.') }}</p>

        <form id="reg-form" class="rs-auth-form" role="form" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">{{ translate('Full Name') }}</label>
                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="{{ translate('Full Name') }}" name="name">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>

            @if (addon_is_activated('otp_system'))
            <div>
                <div id="emailOrPhoneDiv">
                    <div class="form-group phone-form-group mb-1">
                        <label for="phone">{{ translate('Phone') }}</label>
                        <div class="input-group registration-iti">
                            <input type="tel" phone-number id="phone-code" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                            @if(get_setting('customer_registration_verify') == '1')
                            <button class="rs-auth-submit" type="button" id="sendOtpPhoneBtn" onclick="sendVerificationCode(this)" style="width:auto;padding:10px 18px;margin-left:8px;border-radius:8px;">
                                {{ translate('Verify') }}
                            </button>
                            @endif
                        </div>
                    </div>

                    <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code', 'US') }}">

                    <div class="form-group email-form-group mb-1 d-none">
                        <label for="email">{{ translate('Email') }}</label>
                        <div class="input-group">
                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                value="{{ old('email') }}" placeholder="{{ translate('Email') }}" id="signinAddonEmail" name="email" autocomplete="off">
                            @if(get_setting('customer_registration_verify') == '1')
                            <button class="rs-auth-submit" type="button" id="sendOtpBtn" onclick="sendVerificationCode(this)" style="width:auto;padding:10px 18px;margin-left:8px;border-radius:8px;">
                                {{ translate('Verify') }}
                            </button>
                            @endif
                        </div>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                        @endif
                    </div>

                    <div class="form-group text-right mb-0" id="mail_phone_toggle_btn">
                        <button class="btn btn-link p-0 rs-auth-link" type="button" onclick="toggleEmailPhone(this)" style="text-decoration:none;">
                            <i>*{{ translate('Use Email Instead') }}</i>
                        </button>
                    </div>
                </div>
                <div class="form-group mb-3 d-none">
                    <label for="verification_code">{{ translate('Verification Code') }}</label>
                    <div class="input-group">
                        <input type="text"
                            class="form-control @error('verification_code') is-invalid @enderror"
                            name="code" id="verification_code"
                            placeholder="{{ translate('Verification Code') }}"
                            maxlength="6">
                        <span class="btn" id="verifyOtpBtn">
                            <i class="las la-lg la-arrow-right"></i>
                        </span>
                        @error('otp')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            @else
                <div class="form-group email-form-group email-phone-div" id="emailOrPhoneDiv">
                    <label for="email">{{ translate('Email') }}</label>
                    <div class="input-group">
                        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                         name="email" id="signinSrEmail"
                        placeholder="{{ translate('Email Address') }}">
                        @if(get_setting('customer_registration_verify') == '1')
                        <button class="rs-auth-submit" type="button" id="sendOtpBtn" onclick="sendVerificationCode()" style="width:auto;padding:10px 18px;margin-left:8px;border-radius:8px;">
                            {{ translate('Verify') }}
                        </button>
                        @endif
                    </div>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                </div>

                <div class="form-group mb-3 d-none">
                    <label for="verification_code">{{ translate('Verification Code') }}</label>
                    <div class="input-group">
                        <input type="text"
                            class="form-control @error('verification_code') is-invalid @enderror"
                            name="code" id="verification_code"
                            placeholder="{{ translate('Verification Code') }}"
                            maxlength="6">
                        <span class="btn" id="verifyOtpBtn">
                            <i class="las la-lg la-arrow-right"></i>
                        </span>
                        @error('otp')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif

            <div class="form-group mb-0">
                <label for="password">{{ translate('Password') }}</label>
                <div class="position-relative">
                    <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('Password') }}" name="password">
                    <i class="password-toggle las la-2x la-eye"></i>
                </div>
                <div class="text-right mt-1">
                    <span class="fs-12 fw-400" style="color:var(--rs-ink-muted);">{{ translate('Password must contain at least 6 digits') }}</span>
                </div>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('password') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ translate('Confirm Password') }}</label>
                <div class="position-relative">
                    <input type="password" class="form-control" placeholder="{{ translate('Confirm Password') }}" name="password_confirmation">
                    <i class="password-toggle las la-2x la-eye"></i>
                </div>
            </div>

            @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1)
                @if ($errors->has('g-recaptcha-response'))
                    <span class="border invalid-feedback rounded p-2 mb-3 bg-danger text-white" role="alert" style="display: block;"><strong>{{ $errors->first('g-recaptcha-response') }}</strong></span>
                @endif
            @endif

            <label class="rs-auth-checkbox">
                <input type="checkbox" name="checkbox_example_1" required>
                <span>{{ translate('By signing up you agree to our ') }} <a href="{{ route('terms') }}" class="rs-auth-link">{{ translate('terms and conditions.') }}</a></span>
            </label>

            <button type="submit" class="rs-auth-submit" id="createAccountBtn">{{ translate('Create Account') }}</button>
        </form>

        @if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1 || get_setting('apple_login') == 1)
            <div class="rs-auth-divider">{{ translate('Or Join With') }}</div>
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
            {{ translate('Already have an account?') }}
            <a href="{{ route('user.login') }}" class="rs-auth-link">{{ translate('Log In') }}</a>
        </p>

        <a href="{{ url()->previous() }}" class="rs-auth-back">
            <i class="las la-arrow-left fs-16"></i> {{ translate('Back to Previous Page') }}
        </a>
    </div>
</div>
@endsection

@section('script')
@if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('CAPTCHA_KEY') }}"></script>
    <script type="text/javascript">
        document.getElementById('reg-form').addEventListener('submit', function(e) {
            e.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute(`{{ env('CAPTCHA_KEY') }}`, {action: 'register'}).then(function(token) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', 'g-recaptcha-response');
                    input.setAttribute('value', token);
                    e.target.appendChild(input);

                    e.target.submit();
                });
            });
        });
    </script>
@endif
@include('auth.verifyEmailOrPhone')

<script>
    const regVerifyRequired = {{get_setting('customer_registration_verify') ? 'true' : 'false' }};
    const createBtn   = $('#createAccountBtn');
    const termsCheckbox = $('input[name="checkbox_example_1"]');
    function toggleCreateBtn() {
        const termsChecked = termsCheckbox.is(':checked');
        const regVerified  = regVerifyRequired ? (verifyBtn && verifyBtn.classList.contains('disabled')) : true;
        let enableBtn = false;
        if (regVerifyRequired) {
            enableBtn = termsChecked && regVerified;
        } else {
            enableBtn = termsChecked;
        }
        createBtn.prop('disabled', !enableBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleCreateBtn();
        termsCheckbox.on('change', toggleCreateBtn);
    });
</script>
@endsection
