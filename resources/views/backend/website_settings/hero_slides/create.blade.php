@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3 pb-2 border-bottom border-gray">
    <h1 class="h3">{{ translate('Add Hero Slide') }}</h1>
</div>
<div class="row">
    <div class="col-lg-9 mx-auto">
        <form action="{{ route('hero-slides.store') }}" method="POST">
            @csrf
            @include('backend.website_settings.hero_slides._form', ['slide' => null])
        </form>
    </div>
</div>
@endsection
