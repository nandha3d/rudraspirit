@php $rsCurrency = get_system_currency(); @endphp
<footer class="rs-footer">
    <div class="rs-footer-grid">
        <div>
            <div style="display:flex;align-items:center;gap:11px;margin-bottom:20px;">
                <img src="{{ static_asset('assets/img/pages/rudraspirit/Logo_Fav.webp') }}" alt="{{ get_setting('website_name') }}" class="rs-footer-seal">
                <span class="rs-serif" style="font-size:25px;color:var(--rs-cream);font-weight:600;">{{ get_setting('website_name', 'Rudra Spirit') }}</span>
            </div>
            <p style="font-size:15px;color:var(--rs-on-dark-muted);line-height:1.85;max-width:300px;margin:0 0 22px;">
                {{ translate('Hand-picked, lab-certified Rudraksha from the Himalayas. Step in and find the bead that matches your intention.') }}
            </p>
        </div>
        <div>
            <div class="rs-footer-label">{{ translate('Explore') }}</div>
            <div style="display:flex;flex-direction:column;gap:11px;">
                @php $rsParent = rudraspirit_root_category(); @endphp
                <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}">{{ translate('Single Beads') }}</a>
                <a href="{{ $rsParent ? route('products.category', $rsParent->slug) : route('categories.all') }}">{{ translate('Malas') }}</a>
                <a href="{{ route('categories.all') }}">{{ translate('Collections') }}</a>
                <a href="{{ route('blog') }}">{{ translate('Blog') }}</a>
            </div>
        </div>
        <div>
            <div class="rs-footer-label">{{ translate('Help') }}</div>
            <div style="display:flex;flex-direction:column;gap:11px;">
                <a href="{{ route('faq') }}">{{ translate('FAQs') }}</a>
                <a href="{{ route('custom-pages.show_custom_page', 'terms') }}">{{ translate('Terms & Conditions') }}</a>
                <a href="{{ route('custom-pages.show_custom_page', 'privacy-policy') }}">{{ translate('Privacy Policy') }}</a>
                <a href="{{ route('custom-pages.show_custom_page', 'return-policy') }}">{{ translate('Returns & Refunds') }}</a>
            </div>
        </div>
        <div>
            <div class="rs-footer-label">{{ translate('Store Information') }}</div>
            <div style="font-size:15px;color:var(--rs-on-dark-muted);line-height:1.9;">
                {{ translate('Email') }}: {{ get_setting('contact_email') }}<br>
                {{ translate('Phone') }}: {{ get_setting('contact_phone') }}<br>
                {{ get_setting('contact_address') }}
            </div>
        </div>
    </div>
    <div class="rs-footer-bottom">
        <span>&copy; {{ date('Y') }} {{ get_setting('website_name', 'Rudra Spirit') }}. {{ translate('All Rights Reserved.') }}</span>
        <span>{{ $rsCurrency->code ?? '' }} &middot; {{ translate('Shipping Worldwide') }}</span>
    </div>
</footer>
