@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('Expense Categories') }}</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Add Category') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('accounting.categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="fs-13">{{ translate('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">{{ translate('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Categories') }}</h5></div>
            <div class="card-body">
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('Name') }}</th>
                        <th class="text-right">{{ translate('Expenses') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($categories as $c)
                            <tr>
                                <td>{{ $c->name }}</td>
                                <td class="text-right">{{ $c->expenses_count }}</td>
                                <td class="text-right">
                                    <form action="{{ route('accounting.categories.destroy', $c->id) }}" method="POST" onsubmit="return confirm('{{ translate('Delete this category?') }}');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-icon btn-circle btn-sm" type="submit"><i class="las la-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">{{ translate('No categories yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
