<!-- New Address Modal -->
<div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body c-scrollbar-light">
                    <div class="p-3">
                        <!-- Address -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Address')}}</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
                            </div>
                        </div>

                        @if (get_active_countries()->count() > 1)
                        <!-- Country -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Country')}}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control aiz-selectpicker rounded-0" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                        <option value="">{{ translate('Select your country') }}</option>
                                        @foreach (get_active_countries() as $key => $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @elseif(get_active_countries()->count() == 1)
                        <input type="hidden" name="country_id" value="{{get_active_countries()[0]->id }}">
                        @endif
                        @if (get_setting('has_state') == 1)
                        <!-- State -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('State')}}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="state_id" required>

                                </select>
                            </div>
                        </div>
                        @endif

                        <!-- City / location (worldwide autocomplete via Photon/OpenStreetMap) -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('City')}}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3 rs-geo-ac" data-geo-autocomplete>
                                    <input type="text" class="form-control rounded-0 rs-geo-input" autocomplete="off"
                                        placeholder="{{ translate('Search your city / area worldwide...') }}" required>
                                    <div class="rs-geo-suggestions d-none"></div>
                                    <input type="hidden" name="geo_city" class="rs-geo-city">
                                    <input type="hidden" name="geo_state" class="rs-geo-state">
                                    <input type="hidden" name="geo_country" class="rs-geo-country">
                                    <input type="hidden" name="geo_country_code" class="rs-geo-country-code">
                                </div>
                            </div>
                        </div>

                         <!--Area-->
                        <div class="row area-field d-none">
                            <div class="col-md-2">
                                <label>{{ translate('Area')}}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="area_id">

                                </select>
                            </div>
                        </div>

                        @if (get_setting('google_map') == 1)
                            <!-- Google Map -->
                            <div class="row mt-3 mb-3">
                                <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
                                <div id="map"></div>
                                <ul id="geoData">
                                    <li style="display: none;">Full Address: <span id="location"></span></li>
                                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                                    <li style="display: none;">Country: <span id="country"></span></li>
                                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                                </ul>
                            </div>
                            <!-- Longitude -->
                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">{{ translate('Longitude')}}</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3 rounded-0" id="longitude" name="longitude" readonly="">
                                </div>
                            </div>
                            <!-- Latitude -->
                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">{{ translate('Latitude')}}</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3 rounded-0" id="latitude" name="latitude" readonly="">
                                </div>
                            </div>
                        @endif

                        <!-- Postal code -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Postal code')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label>{{ translate('Phone')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="tel" id="phone-code" class="form-control rounded-0" placeholder="" name="phone" autocomplete="off" required>
                                <input type="hidden" name="country_code" value="">
                            </div>
                        </div>

                        <!-- Save button -->
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary rounded-0 w-150px">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    // Worldwide address/city autocomplete using Photon (OpenStreetMap) — free, no API key.
    function initGeoAutocomplete(wrap) {
        if (wrap.dataset.geoInit) return;
        wrap.dataset.geoInit = '1';
        var input = wrap.querySelector('.rs-geo-input');
        var box   = wrap.querySelector('.rs-geo-suggestions');
        var hCity = wrap.querySelector('.rs-geo-city');
        var hState = wrap.querySelector('.rs-geo-state');
        var hCountry = wrap.querySelector('.rs-geo-country');
        var hCode = wrap.querySelector('.rs-geo-country-code');
        var timer;

        input.addEventListener('input', function () {
            var q = input.value.trim();
            hCity.value = q; hState.value = ''; hCountry.value = ''; hCode.value = '';
            clearTimeout(timer);
            if (q.length < 2) { box.classList.add('d-none'); box.innerHTML = ''; return; }
            timer = setTimeout(function () {
                fetch('https://photon.komoot.io/api/?q=' + encodeURIComponent(q) + '&limit=6&lang=en')
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        box.innerHTML = '';
                        var feats = (data && data.features) ? data.features : [];
                        if (!feats.length) {
                            box.innerHTML = '<div class="rs-geo-item text-muted">' + '{{ translate('No matches') }}' + '</div>';
                            box.classList.remove('d-none'); return;
                        }
                        feats.forEach(function (f) {
                            var p = f.properties || {};
                            var parts = [p.name, p.city, p.state, p.country].filter(Boolean);
                            var label = parts.filter(function (v, i, a) { return a.indexOf(v) === i; }).join(', ');
                            var div = document.createElement('div');
                            div.className = 'rs-geo-item';
                            div.textContent = label;
                            div.addEventListener('click', function () {
                                input.value = label;
                                hCity.value = p.city || p.name || p.county || q;
                                hState.value = p.state || p.region || p.county || '';
                                hCountry.value = p.country || '';
                                hCode.value = (p.countrycode || '').toUpperCase();
                                var modal = wrap.closest('.modal') || document;
                                if (p.postcode) {
                                    var pc = modal.querySelector('[name=postal_code]');
                                    if (pc && !pc.value) pc.value = p.postcode;
                                }
                                box.classList.add('d-none'); box.innerHTML = '';
                            });
                            box.appendChild(div);
                        });
                        box.classList.remove('d-none');
                    })
                    .catch(function () {});
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) { box.classList.add('d-none'); }
        });
    }
    function initAll() {
        document.querySelectorAll('[data-geo-autocomplete]').forEach(initGeoAutocomplete);
    }
    if (document.readyState !== 'loading') initAll();
    else document.addEventListener('DOMContentLoaded', initAll);
    window.rsInitGeoAutocomplete = initAll;
})();
</script>

<!-- Edit Address Modal -->
<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Edit Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body c-scrollbar-light" id="edit_modal_body">

            </div>
        </div>
    </div>
</div>

@include('frontend.partials.address.pincode_autofill')