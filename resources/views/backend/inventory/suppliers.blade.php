@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="h3 mb-0">{{ translate('Suppliers') }}</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Add Supplier') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="form-group"><label class="fs-13">{{ translate('Name') }} *</label><input name="name" class="form-control" required></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Email') }}</label><input type="email" name="email" class="form-control"></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Phone') }}</label><input name="phone" class="form-control"></div>
                    <div class="form-group"><label class="fs-13">{{ translate('GSTIN') }}</label><input name="gstin" class="form-control"></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Address') }}</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Note') }}</label><input name="note" class="form-control"></div>
                    <button class="btn btn-primary btn-block" type="submit">{{ translate('Add Supplier') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Supplier List') }}</h5></div>
            <div class="card-body">
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('Name') }}</th><th>{{ translate('Phone') }}</th><th>{{ translate('GSTIN') }}</th><th class="text-right">{{ translate('Options') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($suppliers as $s)
                            <tr>
                                <td>{{ $s->name }}<div class="fs-12 text-muted">{{ $s->email }}</div></td>
                                <td>{{ $s->phone }}</td>
                                <td>{{ $s->gstin }}</td>
                                <td class="text-right text-nowrap">
                                    <button class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="document.getElementById('se-{{ $s->id }}').classList.toggle('d-none')"><i class="las la-edit"></i></button>
                                    <form action="{{ route('suppliers.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ translate('Delete supplier?') }}');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-icon btn-circle btn-sm" type="submit"><i class="las la-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="se-{{ $s->id }}" class="d-none bg-light">
                                <td colspan="4">
                                    <form action="{{ route('suppliers.update', $s->id) }}" method="POST" class="row gutters-5 align-items-end">
                                        @csrf @method('PUT')
                                        <div class="col-md-3"><label class="fs-12">{{ translate('Name') }}</label><input name="name" value="{{ $s->name }}" class="form-control form-control-sm" required></div>
                                        <div class="col-md-3"><label class="fs-12">{{ translate('Email') }}</label><input name="email" value="{{ $s->email }}" class="form-control form-control-sm"></div>
                                        <div class="col-md-2"><label class="fs-12">{{ translate('Phone') }}</label><input name="phone" value="{{ $s->phone }}" class="form-control form-control-sm"></div>
                                        <div class="col-md-2"><label class="fs-12">{{ translate('GSTIN') }}</label><input name="gstin" value="{{ $s->gstin }}" class="form-control form-control-sm"></div>
                                        <div class="col-md-2"><button class="btn btn-sm btn-primary btn-block" type="submit">{{ translate('Save') }}</button></div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">{{ translate('No suppliers yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="aiz-pagination mt-3">{{ $suppliers->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
