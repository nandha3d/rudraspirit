@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3 pb-2 border-bottom border-gray">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('Mukhi Information') }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('mukhi-info.create') }}" class="btn btn-primary rounded-0">
                <i class="las la-plus"></i> {{ translate('Add New Mukhi') }}
            </a>
        </div>
    </div>
</div>

<div class="card rounded-0">
    <div class="card-body">
        <table class="table aiz-table">
            <thead>
                <tr>
                    <th>{{ translate('Mukhi') }}</th>
                    <th>{{ translate('Deity') }}</th>
                    <th>{{ translate('Planet') }}</th>
                    <th>{{ translate('Mantra') }}</th>
                    <th class="text-center">{{ translate('Status') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mukhi_infos as $mukhi)
                    <tr>
                        <td><strong>{{ $mukhi->mukhi_number }} {{ translate('Mukhi') }}</strong></td>
                        <td>{{ $mukhi->deity }}</td>
                        <td>{{ $mukhi->planet }}</td>
                        <td>{{ $mukhi->mantra }}</td>
                        <td class="text-center">
                            @if ($mukhi->status)
                                <span class="badge badge-inline badge-success">{{ translate('Active') }}</span>
                            @else
                                <span class="badge badge-inline badge-secondary">{{ translate('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('mukhi-info.edit', $mukhi->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="{{ route('mukhi-info.destroy', $mukhi->id) }}" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ translate('No mukhi info found. Run migrations to seed the default 1–14 Mukhi data.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
