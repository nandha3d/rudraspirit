@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col"><h1 class="h3 mb-0">{{ translate('Partners') }}</h1></div>
        <div class="col-auto">
            <a href="{{ route('partners.distributions') }}" class="btn btn-soft-primary">{{ translate('Profit Distributions') }}</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Add Partner') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('partners.store') }}" method="POST">
                    @csrf
                    <div class="form-group"><label class="fs-13">{{ translate('Name') }} *</label>
                        <input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Email (login)') }} *</label>
                        <input type="email" name="email" class="form-control" required></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Phone') }}</label>
                        <input type="text" name="phone" class="form-control"></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Password') }} *</label>
                        <input type="text" name="password" class="form-control" required></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Profit Share %') }} *</label>
                        <input type="number" step="0.01" min="0" max="100" name="share_percent" class="form-control" required></div>
                    <div class="form-group"><label class="fs-13">{{ translate('Note') }}</label>
                        <textarea name="note" class="form-control" rows="2"></textarea></div>
                    <button class="btn btn-primary btn-block" type="submit">{{ translate('Add Partner') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 h6">{{ translate('Partner List') }}</h5></div>
            <div class="card-body">
                @php $totalShare = $partners->sum('share_percent'); @endphp
                @if ($totalShare > 100)
                    <div class="alert alert-warning fs-13">{{ translate('Total share percent exceeds 100%') }} ({{ $totalShare }}%).</div>
                @endif
                <table class="table table-bordered aiz-table mb-0">
                    <thead><tr>
                        <th>{{ translate('Name') }}</th>
                        <th class="text-right">{{ translate('Share') }}</th>
                        <th class="text-right">{{ translate('Earned') }}</th>
                        <th class="text-right">{{ translate('Paid') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($partners as $p)
                            <tr>
                                <td>{{ $p->name }}<div class="fs-12 text-muted">{{ $p->email }}</div></td>
                                <td class="text-right">{{ $p->share_percent }}%</td>
                                <td class="text-right">{{ single_price($p->total_earned) }}</td>
                                <td class="text-right">{{ single_price($p->total_paid) }}</td>
                                <td><span class="badge badge-inline {{ $p->status=='active'?'badge-success':'badge-secondary' }}">{{ $p->status }}</span></td>
                                <td class="text-right text-nowrap">
                                    <button class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="document.getElementById('edit-{{ $p->id }}').classList.toggle('d-none')"><i class="las la-edit"></i></button>
                                    <form action="{{ route('partners.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ translate('Delete partner?') }}');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-icon btn-circle btn-sm" type="submit"><i class="las la-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="edit-{{ $p->id }}" class="d-none bg-light">
                                <td colspan="6">
                                    <form action="{{ route('partners.update', $p->id) }}" method="POST" class="row gutters-5 align-items-end">
                                        @csrf @method('PUT')
                                        <div class="col-md-3"><label class="fs-12">{{ translate('Name') }}</label><input name="name" value="{{ $p->name }}" class="form-control form-control-sm" required></div>
                                        <div class="col-md-3"><label class="fs-12">{{ translate('Email') }}</label><input name="email" value="{{ $p->email }}" class="form-control form-control-sm" required></div>
                                        <div class="col-md-2"><label class="fs-12">{{ translate('Share %') }}</label><input type="number" step="0.01" name="share_percent" value="{{ $p->share_percent }}" class="form-control form-control-sm" required></div>
                                        <div class="col-md-2"><label class="fs-12">{{ translate('Status') }}</label>
                                            <select name="status" class="form-control form-control-sm">
                                                <option value="active" @if($p->status=='active') selected @endif>{{ translate('Active') }}</option>
                                                <option value="inactive" @if($p->status=='inactive') selected @endif>{{ translate('Inactive') }}</option>
                                            </select></div>
                                        <div class="col-md-2"><label class="fs-12">{{ translate('New password') }}</label><input name="password" placeholder="{{ translate('leave blank') }}" class="form-control form-control-sm"></div>
                                        <div class="col-12 mt-2"><button class="btn btn-sm btn-primary" type="submit">{{ translate('Save') }}</button></div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">{{ translate('No partners yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
