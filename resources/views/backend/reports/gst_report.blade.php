@extends('backend.layouts.app')

@section('content')
@php
    $taxable = (float) ($summary->taxable_value ?? 0);
    $gst = (float) ($summary->gst ?? 0);
    $cgst = $gst / 2;
    $qs = ['from' => \Carbon\Carbon::parse($from)->format('Y-m-d'), 'to' => \Carbon\Carbon::parse($to)->format('Y-m-d')];
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ translate('GST Report') }}</h1>
            <p class="text-muted fs-13 mb-0">{{ translate('HSN summary & GST collected from order lines. Excludes cancelled orders.') }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('gst_report.hsn_export', $qs) }}" class="btn btn-soft-primary">
                <i class="las la-download"></i> {{ translate('Export HSN (CSV)') }}
            </a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('gst_report.index') }}" method="GET" class="row gutters-5 align-items-end">
            <div class="col-md-3">
                <label class="col-form-label fs-13">{{ translate('From') }}</label>
                <input type="date" name="from" value="{{ $qs['from'] }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="col-form-label fs-13">{{ translate('To') }}</label>
                <input type="date" name="to" value="{{ $qs['to'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-block" type="submit">{{ translate('Filter') }}</button>
            </div>
            <div class="col-md-4 text-md-right text-muted fs-13 mt-2 mt-md-0">
                {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                · {{ (int) ($summary->orders ?? 0) }} {{ translate('orders') }}
            </div>
        </form>
    </div>
</div>

<div class="row gutters-10 mb-1">
    <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Taxable Value') }}</div>
            <div class="h4 fw-700 mb-0">{{ single_price($taxable) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Total GST') }}</div>
            <div class="h4 fw-700 mb-0 text-info">{{ single_price($gst) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('CGST') }}</div>
            <div class="h4 fw-700 mb-0">{{ single_price($cgst) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('SGST') }}</div>
            <div class="h4 fw-700 mb-0">{{ single_price($cgst) }}</div>
        </div></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('GST by rate slab') }}</h5></div>
            <div class="card-body">
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('Rate') }}</th>
                        <th class="text-right">{{ translate('Taxable') }}</th>
                        <th class="text-right">{{ translate('GST') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($byRate as $r)
                            <tr>
                                <td>{{ (float) $r->rate }}%</td>
                                <td class="text-right">{{ single_price($r->taxable_value) }}</td>
                                <td class="text-right">{{ single_price($r->gst) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">{{ translate('No data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('HSN summary') }} <span class="fs-12 text-muted">({{ translate('GSTR-1 Table 12') }})</span></h5></div>
            <div class="card-body">
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('HSN') }}</th>
                        <th>{{ translate('Rate') }}</th>
                        <th class="text-right">{{ translate('Qty') }}</th>
                        <th class="text-right">{{ translate('Taxable') }}</th>
                        <th class="text-right">{{ translate('GST') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($hsn as $h)
                            <tr>
                                <td>{{ $h->hsn }}</td>
                                <td>{{ (float) $h->rate }}%</td>
                                <td class="text-right">{{ (int) $h->qty }}</td>
                                <td class="text-right">{{ single_price($h->taxable_value) }}</td>
                                <td class="text-right">{{ single_price($h->gst) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">{{ translate('No GST data in this period') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<p class="text-muted fs-12">{{ translate('CGST/SGST shown as an intra-state 50/50 split of total GST. For inter-state (IGST) and full GSTR-1 B2B, capture buyer state (place of supply) at checkout.') }}</p>
@endsection
