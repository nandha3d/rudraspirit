@php
    $rsParent = rudraspirit_root_category();
    $rsMukhiProducts = $rsParent ? \App\Models\Product::where('category_id', $rsParent->id)->where('published', 1)->orderBy('id')->get() : collect();
    $rsCartCount = count(get_user_cart());
    $rsCurrency = get_system_currency();
@endphp

<!-- Promotional Live Bar (Screenshot 1) -->
<div class="promotional-banner">
    <div class="promotional-live-label">
        <span class="promotional-live-dot"></span>
        <span>LIVE</span>
    </div>
    <div class="promotional-feed">
        <div class="promotional-track">
            <span class="promotional-item">USA and worldwide</span>
            <span class="promotional-item">&#127873; Custom malas, bracelets and Rudraksha combinations available</span>
            <span class="promotional-item">&#10024; Personal Rudraksha recommendation based on birth details</span>
            <span class="promotional-item">&#128172; Chat with us on WhatsApp: +1 437-267-1257</span>
            <span class="promotional-item">&#127807; 100% ISO Lab Certified Nepal Rudraksha with X-Ray Verification</span>
            {{-- Loop for continuous marquee --}}
            <span class="promotional-item">USA and worldwide</span>
            <span class="promotional-item">&#127873; Custom malas, bracelets and Rudraksha combinations available</span>
            <span class="promotional-item">&#10024; Personal Rudraksha recommendation based on birth details</span>
            <span class="promotional-item">&#128172; Chat with us on WhatsApp: +1 437-267-1257</span>
            <span class="promotional-item">&#127807; 100% ISO Lab Certified Nepal Rudraksha with X-Ray Verification</span>
        </div>
    </div>
    <a href="https://wa.me/14372671257?text=Hello%20Shiva%20Rudraksha%20Inc." target="_blank" rel="noreferrer" class="promotional-cta">
        ENQUIRE
    </a>
</div>

<!-- Luxury Header (Screenshot 1) -->
<header class="luxury-header">
    <div class="luxury-nav">
        <a href="{{ route('home') }}" class="brand-logo" style="text-decoration:none;">
            <img src="{{ asset('images/brand/shiva-rudraksha-logo.png') }}" alt="Shiva Rudraksha Inc." onerror="this.onerror=null;this.src='{{ asset('shivarudraksha/images/brand/shiva-rudraksha-logo.png') }}';">
            <span>
                <strong>SHIVA RUDRAKSHA</strong>
                <span style="font-size:11px;opacity:.75;display:block;letter-spacing:.22em;color:var(--maroon);font-weight:700;">INC.</span>
            </span>
        </a>

        <button type="button" class="mobile-menu-button" onclick="toggleMobileMenu()" aria-label="Toggle navigation">
            <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>

        <nav id="site-nav">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>

            <a href="{{ route('rudraspirit.guide') }}" class="{{ request()->routeIs('rudraspirit.guide') ? 'active' : '' }}">Rudraksha Guide</a>

            <div class="shop-menu knowledge-menu" onmouseenter="this.querySelector('.knowledge-dropdown').style.display='block'" onmouseleave="this.querySelector('.knowledge-dropdown').style.display='none'">
                <a href="{{ route('rudraspirit.knowledge') }}" class="{{ request()->routeIs('rudraspirit.knowledge', 'rudraspirit.maintenance', 'rudraspirit.lord_shiva') ? 'active' : '' }}" style="display:flex;align-items:center;gap:4px;">
                    Knowledge <span style="font-size:10px;opacity:.7;">&#9660;</span>
                </a>
                <div class="shop-dropdown knowledge-dropdown" style="display:none;">
                    <a href="{{ route('rudraspirit.lord_shiva') }}">&#10022; Who is Lord Shiva?</a>
                    <a href="{{ route('rudraspirit.maintenance') }}">&#10022; Rudraksha Maintenance</a>
                    <a href="{{ route('rudraspirit.knowledge') }}">&#10022; Energizing & Do’s/Don’ts</a>
                </div>
            </div>

            <a href="{{ route('rudraspirit.recommendations') }}" class="{{ request()->routeIs('rudraspirit.recommendations') ? 'active' : '' }}">Recommendations</a>
            <a href="{{ route('rudraspirit.about') }}" class="{{ request()->routeIs('rudraspirit.about') ? 'active' : '' }}">About</a>
            <a href="{{ route('rudraspirit.contact') }}" class="{{ request()->routeIs('rudraspirit.contact') ? 'active' : '' }}">Contact</a>
        </nav>

        <div class="nav-actions">
            <!-- Search bar -->
            <div class="rs-header-search" style="position:relative;max-width:210px;">
                <input id="search" name="search" placeholder="{{ translate('Search bead') }}…" style="font-size:13px;padding:9px 34px 9px 14px;border-radius:999px;border:1px solid var(--border);background:#fff;outline:none;width:100%;">
                <span class="rs-header-search-icon" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--maroon);pointer-events:none;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <div class="typed-search-box d-none" style="position:absolute;top:100%;left:0;right:0;z-index:1050;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 12px 28px rgba(0,0,0,.12);margin-top:6px;">
                    <div class="search-preloader d-none"></div>
                    <div class="search-nothing d-none" style="font-size:13px;color:var(--muted);padding:10px 14px;"></div>
                    <div id="search-content"></div>
                </div>
            </div>

            <!-- Currency switcher -->
            @if (get_setting('show_currency_switcher') == 'on')
            <div class="rs-dropdown" id="currency-change" style="position:relative;">
                <span class="rs-dropdown-toggle" style="display:inline-flex;align-items:center;gap:3px;font-size:13px;font-weight:700;color:var(--maroon);padding:7px 10px;background:#fff;border:1px solid var(--border);border-radius:999px;cursor:pointer;">
                    {{ $rsCurrency->code ?? '' }} <span style="font-size:8px;opacity:.6;">&#9660;</span>
                </span>
                <div class="rs-dropdown-menu dropdown-menu" style="min-width:130px;border-radius:12px;padding:8px 0;border:1px solid var(--border);box-shadow:0 10px 24px rgba(0,0,0,.1);">
                    @foreach (get_all_active_currency() as $currency)
                        <a href="javascript:void(0)" data-currency="{{ $currency->code }}" class="dropdown-item @if (isset($rsCurrency->code) && $rsCurrency->code == $currency->code) active @endif" style="font-size:13px;padding:7px 16px;">
                            {{ $currency->code }} ({{ $currency->symbol }})
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Customer Account / Login -->
            @auth
                <a href="{{ route('dashboard') }}" class="outline-button" style="width:auto;min-height:38px;padding:0 14px;font-size:12px;" title="{{ translate('My Account') }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>{{ Str::limit(Auth::user()->name, 10) }}</span>
                </a>
            @else
                <a href="javascript:void(0)" onclick="showLoginModal()" class="outline-button" style="width:auto;min-height:38px;padding:0 14px;font-size:12px;" title="{{ translate('Login') }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Login</span>
                </a>
            @endauth

            <!-- Cart Trigger (hidden while products are in contact-for-details mode) -->
            <a href="javascript:void(0)" class="rs-cart-trigger" title="{{ translate('View Shopping Bag') }}" style="display:none;align-items:center;justify-content:center;position:relative;width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid var(--border);color:var(--maroon);">
                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span class="rs-cart-count cart-count" style="position:absolute;top:-4px;right:-4px;background:var(--maroon);color:#fff;font-size:11px;font-weight:800;width:20px;height:20px;border-radius:50%;display:grid;place-items:center;line-height:1;">{{ $rsCartCount }}</span>
            </a>
            <div id="cart_items" class="d-none"></div>

            <!-- WhatsApp Chat CTA (Themed unified button) -->
            <a class="maroon-button" href="https://wa.me/14372671257" target="_blank" rel="noreferrer" style="min-height:40px;padding:0 18px;font-size:13px;text-decoration:none;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                Chat
            </a>
        </div>
    </div>
</header>

<!-- Mobile bottom navigation bar -->
<nav class="rs-bottom-nav">
    <a href="{{ route('home') }}" class="rs-bottom-nav-item @if (request()->routeIs('home')) active @endif">
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        </span>
        <span>Home</span>
    </a>
    <a href="{{ route('rudraspirit.guide') }}" class="rs-bottom-nav-item @if (request()->routeIs('rudraspirit.guide')) active @endif">
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </span>
        <span>Guide</span>
    </a>
    <a href="{{ route('rudraspirit.recommendations') }}" class="rs-bottom-nav-item @if (request()->routeIs('rudraspirit.recommendations')) active @endif">
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
        </span>
        <span>Recommend</span>
    </a>
    <a href="{{ route('rudraspirit.contact') }}" class="rs-bottom-nav-item @if (request()->routeIs('rudraspirit.contact')) active @endif">
        <span class="rs-bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
        </span>
        <span>Contact</span>
    </a>
</nav>

<!-- Sliding Cart Drawer (Connected to Laravel E-Commerce Engine) -->
<div class="rs-cart-overlay"></div>
<aside class="rs-cart-drawer">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:22px 24px;border-bottom:1px solid var(--border);background:#fffaf3;">
        <span style="font-size:22px;color:var(--maroon);font-family:Georgia,serif;font-weight:600;">Your Sacred Bag</span>
        <span class="rs-cart-close" style="cursor:pointer;font-size:24px;color:var(--muted);width:32px;height:32px;display:grid;place-items:center;border-radius:50%;background:#fff;">&times;</span>
    </div>
    <div class="rs-cart-drawer-body" style="flex:1;overflow-y:auto;padding:14px 24px;"></div>
    <div style="padding:20px 24px;border-top:1px solid var(--border);background:#fffaf3;">
        <div style="display:flex;justify-content:space-between;font-size:18px;color:var(--maroon);margin-bottom:14px;font-family:Georgia,serif;font-weight:600;">
            <span>{{ translate('Subtotal') }}</span>
            <span class="rs-cart-drawer-total"></span>
        </div>
        <a href="{{ route('cart') }}" class="maroon-button" style="display:flex;width:100%;text-align:center;justify-content:center;margin-bottom:10px;text-decoration:none;">
            {{ translate('View Bag & Checkout') }} &rarr;
        </a>
        <a href="javascript:void(0)" class="rs-cart-close" style="display:block;text-align:center;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);text-decoration:none;font-weight:700;">
            {{ translate('Continue Browsing') }}
        </a>
    </div>
</aside>

<script>
function toggleMobileMenu() {
    var nav = document.getElementById('site-nav');
    if (nav) {
        nav.classList.toggle('open');
    }
}

function filterShopCategory(cat) {
    if (window.setCategoryFilter) {
        window.setCategoryFilter(cat);
    } else {
        var buttons = document.querySelectorAll('#shop-category-pills button');
        buttons.forEach(function(btn) {
            if (btn.textContent.trim() === cat || (cat === 'All' && btn.textContent.trim() === 'All')) {
                btn.click();
            }
        });
    }
}

function openSacredBag() {
    var drawer = document.querySelector('.rs-cart-drawer');
    var overlay = document.querySelector('.rs-cart-overlay');
    if (drawer) drawer.classList.add('active');
    if (overlay) overlay.classList.add('active');
    refreshSacredBag();
}

function closeSacredBag() {
    var drawer = document.querySelector('.rs-cart-drawer');
    var overlay = document.querySelector('.rs-cart-overlay');
    if (drawer) drawer.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
}

function refreshSacredBag() {
    var body = document.querySelector('.rs-cart-drawer-body');
    var totalEl = document.querySelector('.rs-cart-drawer-total');
    if (!body) return;

    body.innerHTML = '<div style="text-align:center;padding:40px 20px;color:var(--muted);"><div class="spinner-border spinner-border-sm" role="status" style="color:var(--maroon);margin-bottom:10px;"></div><p style="font-size:13px;margin:0;">Loading sacred bag...</p></div>';

    fetch('{{ route('cart.miniSummary') }}', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        var count = data.cart_count !== undefined ? data.cart_count : (data.items ? data.items.length : 0);
        document.querySelectorAll('.cart-count, .rs-cart-count').forEach(function(el) {
            el.textContent = count;
        });

        if (totalEl) {
            totalEl.textContent = data.total_formatted || '$0.00';
        }

        if (!data.items || data.items.length === 0) {
            body.innerHTML = '<div style="text-align:center;padding:50px 20px;">' +
                '<div style="font-size:42px;margin-bottom:14px;opacity:.6;">&#129695;</div>' +
                '<h4 style="font-family:Georgia,serif;font-size:18px;color:var(--maroon);margin-bottom:8px;">Your Sacred Bag is Empty</h4>' +
                '<p style="font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:20px;">Explore authentic certified Nepal Rudraksha beads, malas, and spiritual combinations.</p>' +
                '<a href="#shop" onclick="closeSacredBag()" class="maroon-button" style="display:inline-flex;text-decoration:none;padding:8px 20px;font-size:13px;">Discover Collection</a>' +
            '</div>';
            return;
        }

        var html = '';
        data.items.forEach(function(item) {
            html += '<div class="rs-cart-drawer-item">' +
                '<img src="' + item.image + '" alt="' + item.name + '" onerror="this.onerror=null;this.src=\'{{ asset('images/products/1-mukhi-nepal-rudraksha/front.jpg') }}\';">' +
                '<div class="rs-cart-drawer-item-info">' +
                    '<a href="' + (item.slug ? ('/product/' + item.slug) : '{{ route('cart') }}') + '" class="rs-cart-drawer-item-title">' + item.name + '</a>' +
                    '<div class="rs-cart-drawer-item-meta">Qty: ' + item.qty + ' &times; ' + item.unit_price_formatted + '</div>' +
                    '<div class="rs-cart-drawer-item-price">' + item.line_total_formatted + '</div>' +
                '</div>' +
                (item.id ? ('<button type="button" class="rs-cart-drawer-remove" onclick="removeSacredBagItem(' + item.id + ')" title="Remove item">&times;</button>') : '') +
            '</div>';
        });

        body.innerHTML = html;
    })
    .catch(function(err) {
        console.error('Error fetching cart summary:', err);
        body.innerHTML = '<div style="text-align:center;padding:30px 15px;color:var(--muted);font-size:13px;">Unable to load bag preview. <a href="{{ route('cart') }}" style="color:var(--maroon);font-weight:700;">View Cart Page</a></div>';
    });
}

function addToSacredBag(productId) {
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    var formData = new URLSearchParams();
    formData.append('id', productId);
    formData.append('quantity', 1);
    if (token) formData.append('_token', token);

    fetch('{{ route('cart.addToCart') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.cart_count !== undefined) {
            document.querySelectorAll('.cart-count, .rs-cart-count').forEach(function(el) {
                el.textContent = data.cart_count;
            });
        }
        openSacredBag();
    })
    .catch(function(err) {
        console.error('Error adding to cart:', err);
        if (typeof addToCartSingleProduct === 'function') {
            addToCartSingleProduct(productId);
        } else {
            window.location.href = '/product/' + productId;
        }
    });
}

function removeSacredBagItem(cartId) {
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    var formData = new URLSearchParams();
    formData.append('id', cartId);
    if (token) formData.append('_token', token);

    fetch('{{ route('cart.removeFromCart') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.cart_count !== undefined) {
            document.querySelectorAll('.cart-count, .rs-cart-count').forEach(function(el) {
                el.textContent = data.cart_count;
            });
        }
        refreshSacredBag();
    })
    .catch(function(err) {
        console.error('Error removing cart item:', err);
        refreshSacredBag();
    });
}

// Bind globals for seamless e-commerce engine integration
window.addToCart = addToSacredBag;
window.addToSacredBag = addToSacredBag;
window.openSacredBag = openSacredBag;
window.closeSacredBag = closeSacredBag;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.rs-cart-trigger').forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            openSacredBag();
        });
    });

    document.querySelectorAll('.rs-cart-close, .rs-cart-overlay').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            closeSacredBag();
        });
    });
});
</script>
