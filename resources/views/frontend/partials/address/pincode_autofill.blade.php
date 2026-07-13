{{-- Indian PIN code -> state/district autofill for address forms.
     Self-hosted lookup at /pincode/{pin}. Delegated on document so it also
     covers modals injected via AJAX. Guarded to run once per page. --}}
<script>
(function () {
    if (window.__rsPincodeInit) return;
    window.__rsPincodeInit = 1;

    var LOOKUP = '{{ url('/pincode') }}';

    function hint(input, msg, cls) {
        if (!input) return;
        var box = input.parentNode;
        var el = box.querySelector('.rs-pin-hint');
        if (!el) {
            el = document.createElement('small');
            el.className = 'rs-pin-hint d-block mt-1';
            box.appendChild(el);
        }
        el.className = 'rs-pin-hint d-block mt-1 ' + (cls || 'text-muted');
        el.textContent = msg || '';
    }

    // Stock dropdown forms: best-effort select an <option> by its text. Fully
    // guarded so a mismatch or missing selectpicker never breaks the form.
    function selectByText(form, name, text) {
        if (!text) return;
        try {
            var sel = form.querySelector('select[name="' + name + '"]');
            if (!sel) return;
            var want = text.trim().toLowerCase();
            for (var i = 0; i < sel.options.length; i++) {
                if ((sel.options[i].text || '').trim().toLowerCase() === want) {
                    sel.value = sel.options[i].value;
                    sel.dispatchEvent(new Event('change', { bubbles: true }));
                    if (window.jQuery && jQuery(sel).selectpicker) {
                        jQuery(sel).selectpicker('refresh');
                    }
                    break;
                }
            }
        } catch (e) {}
    }

    function fill(form, input, data) {
        // Custom rs-geo autocomplete form (new-address modal): fill hidden fields
        // that resolveGeoIds() maps to country/state/city ids by name.
        var set = function (sel, val) {
            var el = form.querySelector(sel);
            if (el && val) el.value = val;
        };
        set('.rs-geo-state', data.state);
        set('.rs-geo-city', data.district);
        set('.rs-geo-country', 'India');
        set('.rs-geo-country-code', 'IN');
        var visible = form.querySelector('.rs-geo-input');
        if (visible && !visible.value) {
            visible.value = [data.district, data.state].filter(Boolean).join(', ');
        }

        // Stock dropdown forms: gently pre-select the matching state.
        selectByText(form, 'state_id', data.state);

        var label = [data.district, data.state].filter(Boolean).join(', ');
        hint(input, '{{ translate('Matched') }}: ' + label, 'text-success');
    }

    var timer;
    document.addEventListener('input', function (e) {
        var t = e.target;
        if (!t || t.getAttribute('name') !== 'postal_code') return;
        var pin = (t.value || '').replace(/\D/g, '');
        clearTimeout(timer);
        if (pin.length !== 6) { hint(t, '', ''); return; }
        var form = t.closest('form') || document;
        timer = setTimeout(function () {
            hint(t, '{{ translate('Looking up PIN…') }}', 'text-muted');
            fetch(LOOKUP + '/' + pin, { headers: { 'Accept': 'application/json' } })
                .then(function (r) {
                    if (r.status === 404) throw 'nf';
                    if (!r.ok) throw 'err';
                    return r.json();
                })
                .then(function (data) {
                    if (data && data.status === 'success') fill(form, t, data);
                    else hint(t, '', '');
                })
                .catch(function (err) {
                    hint(t, err === 'nf' ? '{{ translate('PIN not found — please enter city manually') }}' : '', 'text-muted');
                });
        }, 400);
    });
})();
</script>
