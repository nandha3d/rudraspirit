@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col"><h1 class="h3 mb-0">{{ translate('Profit Distributions') }}</h1></div>
        <div class="col-auto"><a href="{{ route('partners.index') }}" class="btn btn-soft-secondary">{{ translate('Partners') }}</a></div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0 h6">{{ translate('Run a distribution') }}</h5></div>
    <div class="card-body">
        <form action="{{ route('partners.distribution.run') }}" method="POST" class="row gutters-5 align-items-end">
            @csrf
            <div class="col-md-3"><label class="fs-13">{{ translate('From') }}</label>
                <input type="date" name="from" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="form-control" required></div>
            <div class="col-md-3"><label class="fs-13">{{ translate('To') }}</label>
                <input type="date" name="to" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control" required></div>
            <div class="col-md-4"><label class="fs-13">{{ translate('Note') }}</label>
                <input type="text" name="note" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-primary btn-block" type="submit">{{ translate('Compute & Split') }}</button></div>
        </form>
        <p class="fs-12 text-muted mt-2 mb-0">{{ translate('Net profit = revenue − cost − discounts − expenses for the period, split by each active partner\'s share %.') }}</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0 h6">{{ translate('History') }}</h5></div>
    <div class="card-body">
        <table class="table table-bordered aiz-table mb-0">
            <thead><tr>
                <th>{{ translate('Period') }}</th>
                <th class="text-right">{{ translate('Net Profit') }}</th>
                <th class="text-right">{{ translate('Partners') }}</th>
                <th>{{ translate('Status') }}</th>
                <th class="text-right">{{ translate('Options') }}</th>
            </tr></thead>
            <tbody>
                @forelse ($distributions as $d)
                    <tr>
                        <td>{{ $d->period_from->format('d M Y') }} — {{ $d->period_to->format('d M Y') }}</td>
                        <td class="text-right fw-700 {{ $d->net_profit >= 0 ? 'text-success':'text-danger' }}">{{ single_price($d->net_profit) }}</td>
                        <td class="text-right">{{ $d->shares_count }}</td>
                        <td><span class="badge badge-inline badge-soft-secondary text-uppercase">{{ $d->status }}</span></td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('partners.distribution.show', $d->id) }}" class="btn btn-soft-primary btn-sm">{{ translate('View') }}</a>
                            <form action="{{ route('partners.distribution.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ translate('Delete distribution?') }}');">
                                @csrf @method('DELETE')
                                <button class="btn btn-soft-danger btn-icon btn-circle btn-sm" type="submit"><i class="las la-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ translate('No distributions yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="aiz-pagination mt-3">{{ $distributions->links() }}</div>
    </div>
</div>
@endsection
