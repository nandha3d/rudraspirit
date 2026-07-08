@extends('layouts.app')
@section('title', 'New license')

@section('content')
    <h1>New license</h1>
    <div class="card">
        <form method="POST" action="{{ route('licenses.store') }}">
            @csrf
            @include('admin.licenses._form')
            <button class="btn">Create license</button>
            <a class="btn secondary" href="{{ route('licenses.index') }}">Cancel</a>
        </form>
    </div>
@endsection
