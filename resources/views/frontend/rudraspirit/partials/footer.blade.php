@php $rsCurrency = get_system_currency(); @endphp
<footer style="background:#5b080c;color:#fff;padding:65px 0 35px;border-top:1px solid #7c1a20;">
    <div class="container footer-layout" style="display:grid;grid-template-columns:1.4fr 1fr 1fr 1.2fr;gap:35px;margin-bottom:45px;">
        <div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
                <img src="{{ asset('images/brand/shiva-rudraksha-logo.png') }}" alt="Shiva Rudraksha Inc." style="width:48px;height:48px;object-fit:contain;background:#fff;border-radius:50%;padding:2px;" onerror="this.onerror=null;this.src='{{ asset('shivarudraksha/images/brand/shiva-rudraksha-logo.png') }}';">
                <span style="font-family:Georgia,serif;font-size:22px;letter-spacing:.04em;font-weight:600;color:#fff;">
                    SHIVA RUDRAKSHA <small style="display:block;font-size:10px;letter-spacing:.25em;opacity:.8;">INC.</small>
                </span>
            </div>
            <p style="font-size:14px;color:rgba(255,255,255,.8);line-height:1.75;max-width:320px;margin:0 0 18px;">
                Carefully selected, authentic Rudraksha beads directly sourced from Nepal and Indonesia. Each bead individually inspected, photographed, and certified.
            </p>
            <div style="display:flex;gap:10px;">
                <a href="https://wa.me/14372671257" target="_blank" rel="noreferrer" style="display:inline-flex;align-items:center;gap:6px;background:#c9933b;color:#2a0a0c;padding:7px 16px;border-radius:999px;font-size:12px;font-weight:700;text-decoration:none;">
                    &#128172; WhatsApp Us
                </a>
            </div>
        </div>

        <div>
            <strong style="display:block;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:#f9b233;margin-bottom:16px;">Catalogue & Guide</strong>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;">
                <a href="{{ route('rudraspirit.guide') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">1–21 Mukhi Guide</a>
                <a href="{{ route('rudraspirit.recommendations') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Birth Chart Recommendation</a>
                <a href="{{ route('rudraspirit.lord_shiva') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Who is Lord Shiva?</a>
                <a href="{{ route('rudraspirit.maintenance') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Bead Care & Maintenance</a>
                <a href="{{ route('rudraspirit.knowledge') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Energizing & Do’s/Don’ts</a>
                <a href="{{ route('rudraspirit.about') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">About Shiva Rudraksha</a>
                <a href="{{ route('rudraspirit.contact') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Contact Us</a>
            </div>
        </div>

        <div>
            <strong style="display:block;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:#f9b233;margin-bottom:16px;">E-Commerce Store</strong>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;">
                <a href="{{ route('cart') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Shopping Bag (Cart)</a>
                <a href="{{ route('orders.track') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Track Your Order</a>
                @auth
                    <a href="{{ route('dashboard') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">My Account</a>
                @else
                    <a href="javascript:void(0)" onclick="showLoginModal()" style="color:rgba(255,255,255,.85);text-decoration:none;">Customer Login</a>
                @endauth
                <a href="{{ route('custom-pages.show_custom_page', 'terms') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Terms & Conditions</a>
                <a href="{{ route('custom-pages.show_custom_page', 'privacy-policy') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Privacy Policy</a>
                <a href="{{ route('custom-pages.show_custom_page', 'return-policy') }}" style="color:rgba(255,255,255,.85);text-decoration:none;">Returns & Refunds</a>
            </div>
        </div>

        <div>
            <strong style="display:block;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:#f9b233;margin-bottom:16px;">Contact & Sourcing</strong>
            <div style="font-size:14px;color:rgba(255,255,255,.85);line-height:1.85;">
                <div>&#9742; <a href="tel:+14372671257" style="color:inherit;text-decoration:none;">437-267-1257</a></div>
                <div>&#9993; <a href="mailto:shivarudrakshain@gmail.com" style="color:inherit;text-decoration:none;">shivarudrakshain@gmail.com</a></div>
                <div>&#9906; Scarborough, Ontario, Canada</div>
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.15);font-size:13px;color:rgba(255,255,255,.7);">
                    We reply personally. Speak directly with knowledgeable advisors for bead selection.
                </div>
            </div>
        </div>
    </div>

    <div class="container" style="padding-top:24px;border-top:1px solid rgba(255,255,255,.14);display:flex;justify-content:space-between;align-items:center;font-size:13px;color:rgba(255,255,255,.75);flex-wrap:wrap;gap:14px;">
        <span>&copy; {{ date('Y') }} Shiva Rudraksha Inc. All Rights Reserved.</span>
        <span>{{ $rsCurrency->code ?? '' }} &middot; Direct Nepal Sourcing &middot; Shipping Across Canada, USA & Worldwide</span>
    </div>
</footer>
