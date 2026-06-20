@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3 pb-2 border-bottom border-gray">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('Home Hero Slides') }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('hero-slides.create') }}" class="btn btn-primary rounded-0">
                <i class="las la-plus"></i> {{ translate('Add Slide') }}
            </a>
        </div>
    </div>
</div>

@if ($slides->count() == 0)
    <div class="alert alert-info rounded-0">
        {{ translate('No slides yet — the home page is showing the built-in default slides. Add a slide here to take over the hero section.') }}
    </div>
@endif

<div class="card rounded-0">
    <div class="card-body">
        <table class="table aiz-table">
            <thead>
                <tr>
                    <th>{{ translate('Order') }}</th>
                    <th>{{ translate('Image') }}</th>
                    <th>{{ translate('Title') }}</th>
                    <th>{{ translate('Kicker') }}</th>
                    <th class="text-center">{{ translate('Status') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($slides as $slide)
                    <tr>
                        <td>{{ $slide->sort_order }}</td>
                        <td>
                            @if ($slide->image)
                                <img src="{{ uploaded_asset($slide->image) }}" class="size-60px img-fit rounded" alt="slide">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $slide->title }} <em>{{ $slide->title_em }}</em></td>
                        <td>{{ $slide->kicker }}</td>
                        <td class="text-center">
                            @if ($slide->status)
                                <span class="badge badge-inline badge-success">{{ translate('Active') }}</span>
                            @else
                                <span class="badge badge-inline badge-secondary">{{ translate('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('hero-slides.edit', $slide->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="{{ route('hero-slides.destroy', $slide->id) }}" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ translate('No slides found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
