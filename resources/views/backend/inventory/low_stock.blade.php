@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('Low Stock') }}</h1>
    <p class="text-muted fs-13 mb-0">{{ translate('Products at or below their low-stock threshold.') }}</p>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('inventory.low_stock') }}" method="GET" class="form-inline">
            <label class="fs-13 mr-2">{{ translate('Override threshold (optional)') }}</label>
            <input type="number" name="threshold" value="{{ $threshold }}" class="form-control form-control-sm mr-2" style="max-width:120px" placeholder="{{ translate('per-product') }}">
            <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered aiz-table mb-0">
            <thead><tr>
                <th>{{ translate('Product') }}</th>
                <th class="text-right">{{ translate('Current Stock') }}</th>
                <th class="text-right">{{ translate('Low-stock Threshold') }}</th>
                <th>{{ translate('Unit') }}</th>
                <th class="text-right">{{ translate('Options') }}</th>
            </tr></thead>
            <tbody>
                @forelse ($products as $p)
                    <tr class="{{ $p->current_stock <= 0 ? 'table-danger' : '' }}">
                        <td>{{ $p->name }}</td>
                        <td class="text-right fw-700 {{ $p->current_stock <= 0 ? 'text-danger' : 'text-warning' }}">{{ $p->current_stock }}</td>
                        <td class="text-right">{{ $p->low_stock_quantity }}</td>
                        <td>{{ $p->unit }}</td>
                        <td class="text-right"><a href="{{ route('inventory.movements') }}" class="btn btn-soft-primary btn-sm">{{ translate('Adjust') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ translate('No low-stock products') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="aiz-pagination mt-3">{{ $products->links() }}</div>
    </div>
</div>
@endsection
