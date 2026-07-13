@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('New Purchase Order') }}</h1>
</div>

<form action="{{ route('purchase_orders.store') }}" method="POST">
    @csrf
    <div class="card mb-3">
        <div class="card-body row gutters-5">
            <div class="col-md-4">
                <label class="fs-13">{{ translate('Supplier') }}</label>
                <select name="supplier_id" class="form-control aiz-selectpicker" data-live-search="true">
                    <option value="">{{ translate('None') }}</option>
                    @foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="fs-13">{{ translate('Order Date') }} *</label>
                <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="fs-13">{{ translate('Reference') }}</label>
                <input name="reference" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="fs-13">{{ translate('Note') }}</label>
                <input name="note" class="form-control">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 h6">{{ translate('Items') }}</h5>
            <button type="button" class="btn btn-sm btn-soft-primary" onclick="poAddRow()"><i class="las la-plus"></i> {{ translate('Add Item') }}</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered mb-0" id="po-items">
                <thead><tr>
                    <th style="min-width:240px">{{ translate('Product') }}</th>
                    <th style="min-width:120px">{{ translate('Variant') }}</th>
                    <th style="min-width:90px">{{ translate('Qty') }}</th>
                    <th style="min-width:120px">{{ translate('Unit Cost') }}</th>
                    <th style="min-width:120px" class="text-right">{{ translate('Line') }}</th>
                    <th></th>
                </tr></thead>
                <tbody></tbody>
            </table>
            <div class="text-right mt-2 fw-700">{{ translate('Total') }}: <span id="po-total">0.00</span></div>
        </div>
    </div>

    <div class="text-right">
        <a href="{{ route('purchase_orders.index') }}" class="btn btn-light">{{ translate('Cancel') }}</a>
        <button type="submit" class="btn btn-primary">{{ translate('Create Purchase Order') }}</button>
    </div>
</form>

<template id="po-row-tpl">
    <tr>
        <td>
            <select class="form-control aiz-selectpicker" data-live-search="true" name="product_id[]" required>
                <option value="">{{ translate('Select product') }}</option>
                @foreach ($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </select>
        </td>
        <td><input name="variant[]" class="form-control" placeholder="{{ translate('blank = simple') }}"></td>
        <td><input type="number" min="1" step="1" name="qty[]" class="form-control po-qty" value="1"></td>
        <td><input type="number" min="0" step="0.01" name="unit_cost[]" class="form-control po-cost" value="0"></td>
        <td class="text-right po-line">0.00</td>
        <td><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="this.closest('tr').remove(); poRecalc()"><i class="las la-times"></i></button></td>
    </tr>
</template>
@endsection

@section('script')
<script>
    function poAddRow() {
        var tpl = document.getElementById('po-row-tpl').innerHTML;
        document.querySelector('#po-items tbody').insertAdjacentHTML('beforeend', tpl);
        if (typeof AIZ !== 'undefined' && AIZ.plugins) AIZ.plugins.bootstrapSelect('refresh');
    }
    function poRecalc() {
        var total = 0;
        document.querySelectorAll('#po-items tbody tr').forEach(function (tr) {
            var q = parseFloat(tr.querySelector('.po-qty')?.value || 0);
            var c = parseFloat(tr.querySelector('.po-cost')?.value || 0);
            var line = q * c;
            tr.querySelector('.po-line').textContent = line.toFixed(2);
            total += line;
        });
        document.getElementById('po-total').textContent = total.toFixed(2);
    }
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('po-qty') || e.target.classList.contains('po-cost')) poRecalc();
    });
    document.addEventListener('DOMContentLoaded', poAddRow);
</script>
@endsection
