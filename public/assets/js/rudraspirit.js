// Cosmetic countdown used on the home "Limited Time Deal" banner and PDP urgency line.
window.rsStartCountdown = function (el, durationMs) {
    if (!el) return;
    var end = Date.now() + durationMs;
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        var d = Math.max(0, end - Date.now());
        var hours = Math.floor(d / 3600000); d -= hours * 3600000;
        var mins = Math.floor(d / 60000); d -= mins * 60000;
        var secs = Math.floor(d / 1000);
        el.textContent = pad(hours) + 'h ' + pad(mins) + 'm ' + pad(secs) + 's';
    }
    tick();
    setInterval(tick, 1000);
};

// 4-box variant (days/hours/mins/secs) used by the home "Limited Time Deal" banner.
window.rsStartCountdownBoxes = function (daysEl, hoursEl, minsEl, secsEl, durationMs) {
    var end = Date.now() + durationMs;
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        var d = Math.max(0, end - Date.now());
        var days = Math.floor(d / 86400000); d -= days * 86400000;
        var hours = Math.floor(d / 3600000); d -= hours * 3600000;
        var mins = Math.floor(d / 60000); d -= mins * 60000;
        var secs = Math.floor(d / 1000);
        if (daysEl) daysEl.textContent = pad(days);
        if (hoursEl) hoursEl.textContent = pad(hours);
        if (minsEl) minsEl.textContent = pad(mins);
        if (secsEl) secsEl.textContent = pad(secs);
    }
    tick();
    setInterval(tick, 1000);
};

document.addEventListener('DOMContentLoaded', function () {

    // Mega menu (click-toggle on touch, hover on desktop via CSS already)
    document.querySelectorAll('.rs-mega-trigger > a').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            if (window.innerWidth > 991) return;
            e.preventDefault();
            var panel = trigger.closest('.rs-mega-trigger').querySelector('.rs-mega-panel');
            if (panel) panel.classList.toggle('show');
        });
    });

    // Generic dropdown toggle (currency / language)
    document.querySelectorAll('.rs-dropdown-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var menu = toggle.parentElement.querySelector('.rs-dropdown-menu');
            document.querySelectorAll('.rs-dropdown-menu.show').forEach(function (m) {
                if (m !== menu) m.classList.remove('show');
            });
            if (menu) menu.classList.toggle('show');
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('.rs-dropdown-menu.show').forEach(function (m) { m.classList.remove('show'); });
    });

    // Mobile nav toggle
    var mobileToggle = document.querySelector('.rs-mobile-toggle');
    var rsNav = document.querySelector('.rs-nav');
    if (mobileToggle && rsNav) {
        mobileToggle.addEventListener('click', function () {
            rsNav.classList.toggle('show-mobile');
            rsNav.style.display = rsNav.classList.contains('show-mobile') ? 'flex' : '';
        });
    }

    // Cart drawer — reuses the existing cart count element + cart routes already wired in the shared layout
    var cartDrawer = document.querySelector('.rs-cart-drawer');
    var cartOverlay = document.querySelector('.rs-cart-overlay');

    function openCartDrawer() {
        if (!cartDrawer) return;
        cartDrawer.classList.add('show');
        if (cartOverlay) cartOverlay.classList.add('show');
        refreshCartDrawer();
    }
    function closeCartDrawer() {
        if (!cartDrawer) return;
        cartDrawer.classList.remove('show');
        if (cartOverlay) cartOverlay.classList.remove('show');
    }
    function refreshCartDrawer() {
        if (!cartDrawer || typeof AIZ === 'undefined') return;
        var body = cartDrawer.querySelector('.rs-cart-drawer-body');
        if (!body) return;
        fetch(window.location.origin + '/cart/mini-summary', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || !data.items) return;
                if (data.items.length === 0) {
                    body.innerHTML = '<p class="text-center" style="color:#9A8A76;padding:50px 0;">No products in the cart.</p>';
                    return;
                }
                var html = '';
                data.items.forEach(function (it) {
                    var media = it.image
                        ? '<span style="width:60px;height:60px;border-radius:8px;overflow:hidden;flex:none;background:#F2E7D8;"><img src="' + it.image + '" alt="" style="width:100%;height:100%;object-fit:cover;display:block;"></span>'
                        : '<span style="width:60px;height:60px;border-radius:8px;background:linear-gradient(150deg,#F2E7D8,#D7BD9C);display:flex;align-items:center;justify-content:center;flex:none;"><span class="rs-bead" style="width:30px;height:30px;"></span></span>';
                    html += '<div class="rs-cart-item">' +
                        media +
                        '<div style="flex:1;"><div style="font-size:14px;font-family:\'Playfair Display\',serif;">' + it.name + '</div><div style="font-size:12px;color:#9A8A76;margin-top:3px;">' + it.qty + ' &times; ' + it.unit_price_formatted + '</div></div>' +
                        '<div style="font-size:13px;color:#7A4E1E;">' + it.line_total_formatted + '</div>' +
                        '</div>';
                });
                body.innerHTML = html;
                var totalEl = cartDrawer.querySelector('.rs-cart-drawer-total');
                if (totalEl) totalEl.innerHTML = data.total_formatted;
            })
            .catch(function () {});
    }

    document.querySelectorAll('.rs-cart-trigger').forEach(function (el) {
        el.addEventListener('click', function (e) { e.preventDefault(); openCartDrawer(); });
    });
    document.querySelectorAll('.rs-cart-close, .rs-cart-overlay').forEach(function (el) {
        el.addEventListener('click', function () { closeCartDrawer(); });
    });

    window.rsRefreshCartDrawer = refreshCartDrawer;

    // Add to cart with an explicit quantity (PDP qty stepper) — posts to the same
    // cart.addToCart route the rest of the site uses, but reads quantity from the caller
    // instead of the global addToCartSingleProduct() helper which always sends 1.
    window.rsAddToCart = function (productId, quantity) {
        var token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        var body = new URLSearchParams();
        body.append('_token', token);
        body.append('id', productId);
        body.append('quantity', quantity || 1);

        fetch(window.location.origin + '/cart/addtocart', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: body
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.cart_count !== undefined) {
                    document.querySelectorAll('.cart-count').forEach(function (el) { el.innerHTML = data.cart_count; });
                }
                openCartDrawer();
            });
    };

    // Cart page line actions — these controller routes always respond with JSON,
    // so they must be called via fetch (a plain form POST would navigate to raw JSON).
    function rsCsrf() {
        return document.querySelector('meta[name=csrf-token]').getAttribute('content');
    }
    window.rsUpdateCartQty = function (cartId, quantity) {
        var body = new URLSearchParams();
        body.append('_token', rsCsrf());
        body.append('id', cartId);
        body.append('quantity', quantity);
        fetch(window.location.origin + '/cart/updateQuantity', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: body
        }).then(function () { window.location.reload(); });
    };
    window.rsRemoveCartItem = function (cartId) {
        var body = new URLSearchParams();
        body.append('_token', rsCsrf());
        body.append('id', cartId);
        fetch(window.location.origin + '/cart/removeFromCart', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: body
        }).then(function () { window.location.reload(); });
    };

    // Search overlay — wraps the existing #search input / search() function already defined in the shared layout
    var searchOverlay = document.querySelector('.rs-search-overlay');
    document.querySelectorAll('.rs-search-trigger').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            if (searchOverlay) {
                searchOverlay.classList.add('show');
                var input = searchOverlay.querySelector('input[name=search]');
                if (input) input.focus();
            }
        });
    });
    document.querySelectorAll('.rs-search-close').forEach(function (el) {
        el.addEventListener('click', function () {
            if (searchOverlay) searchOverlay.classList.remove('show');
        });
    });

    // Animated hero slider (home page) — auto-rotates, pauses on hover, dots are clickable.
    var rsHero = document.querySelector('.rs-hero');
    if (rsHero) {
        var rsSlides = Array.prototype.slice.call(rsHero.querySelectorAll('.rs-hero-slide'));
        var rsDots = Array.prototype.slice.call(rsHero.querySelectorAll('.rs-hero-dot'));
        var rsCurrent = 0;
        var rsHeroTimer = null;
        var rsHeroDelay = 6500;

        function rsGoToSlide(index) {
            rsCurrent = (index + rsSlides.length) % rsSlides.length;
            rsSlides.forEach(function (slide, i) {
                slide.classList.toggle('active', i === rsCurrent);
            });
            rsDots.forEach(function (dot, i) {
                dot.classList.toggle('active', i === rsCurrent);
            });
        }

        function rsStartHeroTimer() {
            clearInterval(rsHeroTimer);
            rsHeroTimer = setInterval(function () { rsGoToSlide(rsCurrent + 1); }, rsHeroDelay);
        }

        if (rsSlides.length > 1) {
            rsDots.forEach(function (dot, i) {
                dot.addEventListener('click', function () {
                    rsGoToSlide(i);
                    rsStartHeroTimer();
                });
            });
            rsHero.addEventListener('mouseenter', function () { clearInterval(rsHeroTimer); });
            rsHero.addEventListener('mouseleave', rsStartHeroTimer);
            rsStartHeroTimer();
        }
    }

    // Floating quick-action buttons (All Categories / Flash Sale / Today's Deal) —
    // only reveal on desktop once the cursor gets near them, otherwise stay hidden.
    var rsFloatSection = document.querySelector('.floating-buttons-section');
    if (rsFloatSection) {
        var rsFloatNearPx = 130;
        document.addEventListener('mousemove', function (e) {
            if (window.innerWidth <= 991) return;
            var rect = rsFloatSection.getBoundingClientRect();
            var near = e.clientX <= rect.right + rsFloatNearPx &&
                e.clientY >= rect.top - rsFloatNearPx &&
                e.clientY <= rect.bottom + rsFloatNearPx;
            rsFloatSection.classList.toggle('rs-near', near);
        });
    }
});
