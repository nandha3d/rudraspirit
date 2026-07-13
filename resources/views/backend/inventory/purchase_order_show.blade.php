@extends('backend.layouts.app')

@section('content')
@php $badge = ['draft'=>'secondary','ordered'=>'info','received'=>'success','cancelled'=>'danger']; @endphp
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ translate('Purchase Order') }} #{{ $order->id }}</h1>
            <p class="text-muted fs-13 mb-0">
                {{ optional($order->supplier)->name ?? translate('No supplier') }} ·
                {{ optional($order->order_date)->format('d M Y') }} ·
                <span class="badge badge-inline badge-{{ $badge[$order->status] ?? 'secondary' }} text-uppercase">{{ $order->status }}</span>
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('purchase_orders.index') }}" class="btn btn-soft-secondary">{{ translate('Back') }}</a>
            @if ($order->status !== 'received')
                <form action="{{ route('purchase_orders.receive', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ translate('Receive stock for this PO? This updates inventory.') }}');">
                    @csrf
                    <button class="btn btn-success" type="submit"><i class="las la-check"></i> {{ translate('Receive Stock') }}</button>
                </form>
                <form action="{{ route('purchase_orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ translate('Delete this PO?') }}');">
                    @csrf @method('DELETE')
                    <button class="btn btn-soft-danger" type="submit"><i class="las la-trash"></i></button>
                </form>
            @else
                <span class="text-success fs-13"><i class="las la-check-circle"></i> {{ translate('Received') }} {{ optional($order->received_at)->format('d M Y H:i') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered aiz-table mb-0">
            <thead><tr>
                <th>{{ translate('Product') }}</th><th>{{ translate('Variant') }}</th>
                <th class="text-right">{{ translate('Qty') }}</th><th class="text-right">{{ translate('Unit Cost') }}</th><th class="text-right">{{ translate('Line') }}</th>
            </tr></thead>
            <tbody>
                @foreach ($order->items as $it)
                    <tr>
                        <td>{{ optional($it->product)->name ?? ('#'.$it->product_id) }}</td>
                        <td>{{ $it->variant ?: '—' }}</td>
                        <td class="text-right">{{ $it->qty }}</td>
                        <td class="text-right">{{ single_price($it->unit_cost) }}</td>
                        <td class="text-right">{{ single_price($it->qty * $it->unit_cost) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot><tr class="fw-700"><td colspan="4" class="text-right">{{ translate('Total') }}</td><td class="text-right">{{ single_price($order->total) }}</td></tr></tfoot>
        </table>
        @if ($order->note)<p class="fs-13 text-muted mt-2 mb-0">{{ translate('Note') }}: {{ $order->note }}</p>@endif
    </div>
</div>
@endsection
