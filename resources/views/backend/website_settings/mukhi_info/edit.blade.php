@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3 pb-2 border-bottom border-gray">
    <h1 class="h3">{{ translate('Edit') }} {{ $mukhi_info->mukhi_number }} {{ translate('Mukhi') }}</h1>
</div>
<div class="row">
    <div class="col-lg-9 mx-auto">
        <form action="{{ route('mukhi-info.update', $mukhi_info->id) }}" method="POST">
            @csrf
            @include('backend.website_settings.mukhi_info._form', ['mukhi_info' => $mukhi_info])
        </form>
    </div>
</div>
@endsection
