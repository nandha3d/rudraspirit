@extends('frontend.layouts.app')

@section('meta_title'){{ $page->meta_title }}@stop
@section('meta_description'){{ $page->meta_description }}@stop

@section('content')
@php
    $lang = str_replace('_', '-', app()->getLocale());
    $content = json_decode($page->getTranslation('content', $lang));
@endphp
<section style="background:var(--rs-cream);padding:54px 32px;text-align:center;">
    <div style="font-size:13px;letter-spacing:.24em;text-transform:uppercase;color:var(--rs-gold);">{{ translate('Home') }} / {{ translate('Contact') }}</div>
    <h1 class="rs-serif" style="font-weight:500;font-size:45px;letter-spacing:.06em;text-transform:uppercase;color:var(--rs-ink);margin:14px 0 0;">{{ translate('Get in') }} <em style="color:var(--rs-gold);font-style:italic;">{{ translate('Touch') }}</em></h1>
</section>

<section style="max-width:1080px;margin:0 auto;padding:60px 32px 90px;display:grid;grid-template-columns:1.3fr 1fr;gap:56px;">
    <form id="contact-us" action="{{ route('contact') }}" method="POST" style="display:flex;flex-direction:column;gap:18px;">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <input name="name" required value="{{ old('name') }}" placeholder="{{ translate('Your name') }}" style="border:1px solid var(--rs-tan);border-radius:8px;padding:14px 16px;font-family:'Jost',sans-serif;font-size:17px;outline:none;">
            <input type="email" name="email" required value="{{ old('email') }}" placeholder="{{ translate('Email address') }}" style="border:1px solid var(--rs-tan);border-radius:8px;padding:14px 16px;font-family:'Jost',sans-serif;font-size:17px;outline:none;">
        </div>
        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="{{ translate('Phone (optional)') }}" style="border:1px solid var(--rs-tan);border-radius:8px;padding:14px 16px;font-family:'Jost',sans-serif;font-size:17px;outline:none;">
        <textarea name="content" rows="6" required placeholder="{{ translate('How can we help?') }}" style="border:1px solid var(--rs-tan);border-radius:8px;padding:14px 16px;font-family:'Jost',sans-serif;font-size:17px;outline:none;resize:vertical;"></textarea>

        @if (get_setting('google_recaptcha') == 1 && get_setting('recaptcha_contact_form') == 1 && $errors->has('g-recaptcha-response'))
            <span style="color:var(--rs-danger);font-size:15px;">{{ $errors->first('g-recaptcha-response') }}</span>
        @endif

        @if (env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
            <a href="javascript:void(0)" class="rs-btn" style="align-self:flex-start;" onclick="AIZ.plugins.notify('warning', '{{ translate('Something went wrong.') }}')">{{ translate('Send Message') }}</a>
        @else
            <button type="submit" class="rs-btn" style="align-self:flex-start;">{{ translate('Send Message') }}</button>
        @endif
    </form>

    <div style="background:var(--rs-cream);border-radius:10px;padding:34px;">
        <h3 class="rs-serif" style="font-size:26px;font-weight:500;color:var(--rs-ink);margin:0 0 22px;">{{ translate('Store Information') }}</h3>
        <div style="font-size:16px;color:var(--rs-ink-soft);line-height:2.2;">
            <div><strong style="color:var(--rs-brown);">{{ translate('Address') }}</strong><br>{!! str_replace("\n", '<br>', $content->address ?? get_setting('contact_address')) !!}</div>
            <div style="margin-top:14px;"><strong style="color:var(--rs-brown);">{{ translate('Phone') }}</strong><br>{{ $content->phone ?? get_setting('contact_phone') }}</div>
            <div style="margin-top:14px;"><strong style="color:var(--rs-brown);">{{ translate('Email') }}</strong><br>{{ $content->email ?? get_setting('contact_email') }}</div>
        </div>
    </div>
</section>
@endsection

@section('script')
@if (get_setting('google_recaptcha') == 1 && get_setting('recaptcha_contact_form') == 1)
<script src="https://www.google.com/recaptcha/api.js?render={{ env('CAPTCHA_KEY') }}"></script>
<script>
    document.getElementById('contact-us').addEventListener('submit', function (e) {
        e.preventDefault();
        grecaptcha.ready(function () {
            grecaptcha.execute('{{ env('CAPTCHA_KEY') }}', { action: 'contact_us' }).then(function (token) {
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
@endsection
