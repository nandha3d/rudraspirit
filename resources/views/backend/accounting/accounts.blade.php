@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('Financial Accounts') }}</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Add Account') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('accounting.accounts.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="{{ translate('Cash, HDFC Bank, ...') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Type') }}</label>
                        <select name="type" class="form-control aiz-selectpicker">
                            <option value="cash">{{ translate('Cash') }}</option>
                            <option value="bank">{{ translate('Bank') }}</option>
                            <option value="wallet">{{ translate('Wallet') }}</option>
                            <option value="gateway">{{ translate('Payment Gateway') }}</option>
                            <option value="other">{{ translate('Other') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Opening Balance') }}</label>
                        <input type="number" step="0.01" name="opening_balance" value="0" class="form-control">
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">{{ translate('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Accounts') }}</h5></div>
            <div class="card-body">
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('Name') }}</th>
                        <th>{{ translate('Type') }}</th>
                        <th class="text-right">{{ translate('Opening') }}</th>
                        <th class="text-right">{{ translate('Current') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($accounts as $a)
                            <tr>
                                <td>{{ $a->name }}</td>
                                <td><span class="badge badge-inline badge-soft-secondary text-uppercase">{{ $a->type }}</span></td>
                                <td class="text-right">{{ single_price($a->opening_balance) }}</td>
                                <td class="text-right fw-700">{{ single_price($a->current_balance) }}</td>
                                <td class="text-right">
                                    <form action="{{ route('accounting.accounts.destroy', $a->id) }}" method="POST" onsubmit="return confirm('{{ translate('Delete this account?') }}');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-icon btn-circle btn-sm" type="submit"><i class="las la-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ translate('No accounts yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
