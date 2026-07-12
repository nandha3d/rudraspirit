@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('Expenses') }}</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Add Expense') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('accounting.expenses.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Tax') }}</label>
                        <input type="number" step="0.01" min="0" name="tax" value="0" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Category') }}</label>
                        <select name="expense_category_id" class="form-control aiz-selectpicker">
                            <option value="">{{ translate('None') }}</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Paid from account') }}</label>
                        <select name="financial_account_id" class="form-control aiz-selectpicker">
                            <option value="">{{ translate('None') }}</option>
                            @foreach ($accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Payee') }}</label>
                        <input type="text" name="payee" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Reference') }}</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Note') }}</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">{{ translate('Save Expense') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                <h5 class="mb-0 h6">{{ translate('Expense List') }}</h5>
                <form action="{{ route('accounting.expenses') }}" method="GET" class="form-inline">
                    <input type="date" name="from" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}" class="form-control form-control-sm mr-1">
                    <input type="date" name="to" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}" class="form-control form-control-sm mr-1">
                    <button class="btn btn-sm btn-soft-primary" type="submit">{{ translate('Filter') }}</button>
                </form>
            </div>
            <div class="card-body">
                <div class="mb-2 fw-700">{{ translate('Total') }}: {{ single_price($total) }}</div>
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Category') }}</th>
                        <th>{{ translate('Payee') }}</th>
                        <th class="text-right">{{ translate('Amount') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($expenses as $e)
                            <tr>
                                <td>{{ $e->date ? $e->date->format('d M Y') : '' }}</td>
                                <td>{{ optional($e->category)->name ?? '—' }}</td>
                                <td>{{ $e->payee }}<div class="fs-12 text-muted">{{ $e->note }}</div></td>
                                <td class="text-right">{{ single_price($e->amount + $e->tax) }}</td>
                                <td class="text-right">
                                    <form action="{{ route('accounting.expenses.destroy', $e->id) }}" method="POST" onsubmit="return confirm('{{ translate('Delete this expense?') }}');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-icon btn-circle btn-sm" type="submit"><i class="las la-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ translate('No expenses in this period') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="aiz-pagination mt-3">{{ $expenses->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
