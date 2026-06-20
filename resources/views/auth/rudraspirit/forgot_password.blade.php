<div class="rs-auth-card">
    <div class="rs-auth-visual">
        <img src="{{ uploaded_asset(get_setting('forgot_password_page_image')) }}" alt="{{ translate('Forgot Password Page Image') }}">
    </div>

    <div class="rs-auth-form-side">
        <div class="rs-auth-kicker">{{ translate('Account Recovery') }}</div>
        <h1 class="rs-auth-title">{{ translate('Forgot password?') }}</h1>
        <p class="rs-auth-sub">
            {{ addon_is_activated('otp_system') ?
                translate('Enter your email address or phone number to recover your password.') :
                    translate('Enter your email address to recover your password.') }}
        </p>

        <form class="rs-auth-form" id="forgot-pass-form" role="form" action="{{ route('password.email') }}" method="POST">
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
                    <button class="btn btn-link p-0 rs-auth-link" type="button" onclick="toggleEmailPhone(this)" style="text-decoration:none;"><i>*{{ translate('Use Email Instead') }}</i></button>
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

            @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_forgot_password') == 1)
                @if ($errors->has('g-recaptcha-response'))
                    <span class="border invalid-feedback rounded p-2 mb-3 bg-danger text-white" role="alert" style="display: block;"><strong>{{ $errors->first('g-recaptcha-response') }}</strong></span>
                @endif
            @endif

            <div class="mt-4">
                <button type="submit" class="rs-auth-submit">{{ translate('Send Password Reset Code') }}</button>
            </div>
        </form>

        <a href="{{ url()->previous() }}" class="rs-auth-back">
            <i class="las la-arrow-left fs-16"></i> {{ translate('Back to Previous Page') }}
        </a>
    </div>
</div>
