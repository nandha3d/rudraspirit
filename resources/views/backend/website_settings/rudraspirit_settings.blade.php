@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3 pb-2 border-bottom border-gray">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('RudraSpirit Theme Settings') }}</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <form action="{{ route('business_settings.update') }}" method="POST">
            @csrf

            {{-- Storefront --}}
            <div class="card rounded-0">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Storefront') }}</h5>
                </div>
                <div class="card-body">
                    <input type="hidden" name="types[]" value="rudraspirit_root_category">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">{{ translate('Root Catalog Category') }}</label>
                        <div class="col-md-8">
                            @php $rsRootCat = get_setting('rudraspirit_root_category', 'rudraksha-beads'); @endphp
                            <select name="rudraspirit_root_category" class="form-control aiz-selectpicker" data-live-search="true">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}" @if ($rsRootCat == $category->slug) selected @endif>
                                        {{ $category->getTranslation('name') }} ({{ $category->slug }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ translate('Main category powering the menu, mukhi rail and shop links. Default: rudraksha-beads') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Limited Time Deal --}}
            <div class="card rounded-0 mt-3">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Home "Limited Time Deal" Countdown') }}</h5>
                </div>
                <div class="card-body">
                    <input type="hidden" name="types[]" value="rudraspirit_deal_end">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">{{ translate('Deal End Date & Time') }}</label>
                        <div class="col-md-8">
                            @php
                                $rsDealEndRaw = get_setting('rudraspirit_deal_end');
                                $rsDealEndVal = $rsDealEndRaw ? date('Y-m-d\TH:i', is_numeric($rsDealEndRaw) ? (int) $rsDealEndRaw : strtotime($rsDealEndRaw)) : '';
                            @endphp
                            <input type="datetime-local" name="rudraspirit_deal_end" value="{{ $rsDealEndVal }}" class="form-control rounded-0">
                            <small class="text-muted">{{ translate('Leave empty to use an automatic rolling countdown (set by the cycle below).') }}</small>
                        </div>
                    </div>

                    <input type="hidden" name="types[]" value="rudraspirit_deal_cycle_hours">
                    <div class="form-group row mb-0">
                        <label class="col-md-4 col-form-label">{{ translate('Rolling Cycle (hours)') }}</label>
                        <div class="col-md-8">
                            <input type="number" min="1" name="rudraspirit_deal_cycle_hours" value="{{ get_setting('rudraspirit_deal_cycle_hours', 72) }}" class="form-control rounded-0">
                            <small class="text-muted">{{ translate('Used only when no end date is set. Default: 72 hours.') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-primary rounded-0">{{ translate('Save Settings') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
