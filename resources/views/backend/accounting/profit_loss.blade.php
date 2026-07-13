@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('Profit & Loss') }}</h1>
    <p class="text-muted fs-13 mb-0">{{ translate('Sales (net of cost) minus expenses. Excludes cancelled orders.') }}</p>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('accounting.profit_loss') }}" method="GET" class="row gutters-5 align-items-end">
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
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Statement') }}</h5></div>
            <div class="card-body">
                <table class="table mb-0">
                    <tbody>
                        <tr><td>{{ translate('Revenue (net sales)') }}</td><td class="text-right fw-600">{{ single_price($revenue) }}</td></tr>
                        <tr><td class="pl-4 text-muted">{{ translate('less Cost of Goods Sold (COGS)') }}</td><td class="text-right text-warning">- {{ single_price($cogs) }}</td></tr>
                        <tr class="border-top"><td class="fw-700">{{ translate('Gross Profit') }}</td><td class="text-right fw-700 text-info">{{ single_price($grossProfit) }}</td></tr>
                        <tr><td class="pl-4 text-muted">{{ translate('less Discounts') }}</td><td class="text-right text-danger">- {{ single_price($discount) }}</td></tr>
                        <tr><td class="pl-4 text-muted">{{ translate('less Total Expenses') }}</td><td class="text-right text-danger">- {{ single_price($totalExpenses) }}</td></tr>
                        <tr class="border-top"><td class="fw-700 fs-16">{{ translate('Net Profit') }}</td><td class="text-right fw-700 fs-16 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ single_price($netProfit) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Expenses by category') }}</h5></div>
            <div class="card-body">
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr><th>{{ translate('Category') }}</th><th class="text-right">{{ translate('Amount') }}</th></tr></thead>
                    <tbody>
                        @forelse ($expensesByCat as $row)
                            <tr><td>{{ $row->category }}</td><td class="text-right">{{ single_price($row->total) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">{{ translate('No expenses') }}</td></tr>
                        @endforelse
                    </tbody>
                    @if (count($expensesByCat))
                        <tfoot><tr class="fw-700"><td>{{ translate('Total') }}</td><td class="text-right">{{ single_price($totalExpenses) }}</td></tr></tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
