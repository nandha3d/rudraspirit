@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ translate('Distribution') }}</h1>
            <p class="text-muted fs-13 mb-0">{{ $distribution->period_from->format('d M Y') }} — {{ $distribution->period_to->format('d M Y') }}</p>
        </div>
        <div class="col-auto"><a href="{{ route('partners.distributions') }}" class="btn btn-soft-secondary">{{ translate('Back') }}</a></div>
    </div>
</div>

<div class="row gutters-10 mb-2">
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Net Profit') }}</div>
            <div class="h4 fw-700 mb-0 {{ $distribution->net_profit >= 0 ? 'text-success':'text-danger' }}">{{ single_price($distribution->net_profit) }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Total Allocated') }}</div>
            <div class="h4 fw-700 mb-0">{{ single_price($distribution->shares->sum('amount')) }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <div class="fs-12 text-muted text-uppercase">{{ translate('Paid') }}</div>
            <div class="h4 fw-700 mb-0 text-success">{{ single_price($distribution->shares->where('paid', true)->sum('amount')) }}</div>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0 h6">{{ translate('Partner shares') }}</h5></div>
    <div class="card-body">
        <table class="table table-bordered aiz-table mb-0">
            <thead><tr>
                <th>{{ translate('Partner') }}</th>
                <th class="text-right">{{ translate('Share %') }}</th>
                <th class="text-right">{{ translate('Amount') }}</th>
                <th>{{ translate('Status') }}</th>
                <th class="text-right">{{ translate('Action') }}</th>
            </tr></thead>
            <tbody>
                @foreach ($distribution->shares as $s)
                    <tr>
                        <td>{{ optional($s->partner)->name ?? '—' }}</td>
                        <td class="text-right">{{ $s->share_percent }}%</td>
                        <td class="text-right fw-700">{{ single_price($s->amount) }}</td>
                        <td>
                            @if ($s->paid)
                                <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                <span class="fs-12 text-muted">{{ optional($s->paid_at)->format('d M Y') }}</span>
                            @else
                                <span class="badge badge-inline badge-warning">{{ translate('Pending') }}</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <form action="{{ route('partners.share.paid', $s->id) }}" method="POST" class="form-inline justify-content-end">
                                @csrf
                                @if (!$s->paid)
                                    <input type="text" name="method" placeholder="{{ translate('Method') }}" class="form-control form-control-sm mr-1" style="max-width:110px">
                                    <input type="text" name="reference" placeholder="{{ translate('Ref') }}" class="form-control form-control-sm mr-1" style="max-width:110px">
                                @endif
                                <button class="btn btn-sm {{ $s->paid ? 'btn-soft-secondary' : 'btn-soft-success' }}" type="submit">
                                    {{ $s->paid ? translate('Mark Unpaid') : translate('Mark Paid') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
