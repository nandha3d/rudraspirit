@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('Stock Movements') }}</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Manual Adjustment') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('inventory.adjust') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Product') }} *</label>
                        <select name="product_id" class="form-control aiz-selectpicker" data-live-search="true" required>
                            <option value="">{{ translate('Select product') }}</option>
                            @foreach ($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="fs-13">{{ translate('Variant') }}</label><input name="variant" class="form-control" placeholder="{{ translate('blank = simple') }}"></div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Quantity (+in / -out)') }} *</label>
                        <input type="number" name="qty" class="form-control" placeholder="e.g. 5 or -3" required>
                    </div>
                    <div class="form-group"><label class="fs-13">{{ translate('Reference') }}</label><input name="reference" class="form-control"></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Note') }}</label><input name="note" class="form-control"></div>
                    <button class="btn btn-primary btn-block" type="submit">{{ translate('Apply Adjustment') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Movement Log') }}</h5></div>
            <div class="card-body">
                @php $tb = ['purchase'=>'success','adjustment'=>'info','correction'=>'warning']; @endphp
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('Date') }}</th><th>{{ translate('Product') }}</th><th>{{ translate('Type') }}</th>
                        <th class="text-right">{{ translate('Qty') }}</th><th>{{ translate('Ref') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($movements as $m)
                            <tr>
                                <td>{{ $m->created_at ? $m->created_at->format('d M Y H:i') : '' }}</td>
                                <td>{{ optional($m->product)->name ?? ('#'.$m->product_id) }}<div class="fs-12 text-muted">{{ $m->variant }}</div></td>
                                <td><span class="badge badge-inline badge-{{ $tb[$m->type] ?? 'secondary' }} text-uppercase">{{ $m->type }}</span></td>
                                <td class="text-right fw-700 {{ $m->qty >= 0 ? 'text-success' : 'text-danger' }}">{{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}</td>
                                <td>{{ $m->reference }}<div class="fs-12 text-muted">{{ $m->note }}</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ translate('No movements yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="aiz-pagination mt-3">{{ $movements->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
