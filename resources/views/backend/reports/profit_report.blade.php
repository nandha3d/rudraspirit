@extends('backend.layouts.app')

@section('content')
@php
    $revenue = (float) ($summary->revenue ?? 0);
    $cost = (float) ($summary->cost ?? 0);
    $discount = (float) ($summary->discount ?? 0);
    $grossProfit = $revenue - $cost;
    $netProfit = $revenue - $cost - $discount;
    $margin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Profit Report') }}</h1>
        <p class="text-muted fs-13 mb-0">{{ translate('Revenue vs cost from the per-order cost snapshot. Excludes cancelled orders.') }}</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('profit_report.index') }}" method="GET" class="row gutters-5 align-items-end">
            <div class="col-md-3">
                <label class="col-form-label fs-13">{{ translate('From') }}</label>
                <input type="date" name="from" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="col-form-label fs-13">{{ translate('To') }}</label>
                <input type="date" name="to" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-block" type="submit">{{ translate('Filter') }}</button>
            </div>
            <div class="col-md-4 text-md-right text-muted fs-13 mt-2 mt-md-0">
                {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                · {{ (int) ($summary->orders ?? 0) }} {{ translate('orders') }}, {{ (int) ($summary->qty ?? 0) }} {{ translate('units') }}
            </div>
        </form>
    </div>
</div>

<div class="row gutters-10 mb-1">
    <div class="col-6 col-lg">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Revenue') }}</div>
            <div class="h4 fw-700 mb-0">{{ single_price($revenue) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Cost (COGS)') }}</div>
            <div class="h4 fw-700 mb-0 text-warning">{{ single_price($cost) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Gross Profit') }}</div>
            <div class="h4 fw-700 mb-0 text-info">{{ single_price($grossProfit) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Discounts') }}</div>
            <div class="h4 fw-700 mb-0 text-danger">-{{ single_price($discount) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Net Profit') }}</div>
            <div class="h4 fw-700 mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ single_price($netProfit) }}</div>
            <div class="fs-12 text-muted">{{ translate('Margin') }} {{ number_format($margin, 1) }}%</div>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Top products by profit') }}</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Product') }}</th>
                    <th class="text-right">{{ translate('Units') }}</th>
                    <th class="text-right">{{ translate('Revenue') }}</th>
                    <th class="text-right">{{ translate('Cost') }}</th>
                    <th class="text-right">{{ translate('Profit') }}</th>
                    <th class="text-right">{{ translate('Margin') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($top as $i => $row)
                    @php $rMargin = $row->revenue > 0 ? ($row->profit / $row->revenue) * 100 : 0; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->name }}</td>
                        <td class="text-right">{{ (int) $row->qty }}</td>
                        <td class="text-right">{{ single_price($row->revenue) }}</td>
                        <td class="text-right text-warning">{{ single_price($row->cost) }}</td>
                        <td class="text-right fw-700 {{ $row->profit >= 0 ? 'text-success' : 'text-danger' }}">{{ single_price($row->profit) }}</td>
                        <td class="text-right">{{ number_format($rMargin, 1) }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">{{ translate('No sales in this period') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if (count($top) && $cost == 0)
            <p class="text-muted fs-12 mt-2 mb-0">{{ translate('Tip: set Cost / Purchase Price on your products so cost and profit are calculated. Orders placed before that stay at zero cost.') }}</p>
        @endif
    </div>
</div>
@endsection
