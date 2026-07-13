@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col"><h1 class="h3 mb-0">{{ translate('Purchase Orders') }}</h1></div>
        <div class="col-auto"><a href="{{ route('purchase_orders.create') }}" class="btn btn-primary">{{ translate('New Purchase Order') }}</a></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered aiz-table mb-0">
            <thead><tr>
                <th>#</th><th>{{ translate('Supplier') }}</th><th>{{ translate('Date') }}</th>
                <th class="text-right">{{ translate('Items') }}</th><th class="text-right">{{ translate('Total') }}</th>
                <th>{{ translate('Status') }}</th><th class="text-right">{{ translate('Options') }}</th>
            </tr></thead>
            <tbody>
                @php $badge = ['draft'=>'secondary','ordered'=>'info','received'=>'success','cancelled'=>'danger']; @endphp
                @forelse ($orders as $o)
                    <tr>
                        <td>{{ $o->id }}<div class="fs-12 text-muted">{{ $o->reference }}</div></td>
                        <td>{{ optional($o->supplier)->name ?? '—' }}</td>
                        <td>{{ optional($o->order_date)->format('d M Y') }}</td>
                        <td class="text-right">{{ $o->items_count }}</td>
                        <td class="text-right">{{ single_price($o->total) }}</td>
                        <td><span class="badge badge-inline badge-{{ $badge[$o->status] ?? 'secondary' }} text-uppercase">{{ $o->status }}</span></td>
                        <td class="text-right"><a href="{{ route('purchase_orders.show', $o->id) }}" class="btn btn-soft-primary btn-sm">{{ translate('View') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">{{ translate('No purchase orders yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="aiz-pagination mt-3">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
