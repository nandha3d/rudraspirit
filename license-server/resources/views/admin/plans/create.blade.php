@extends('layouts.app')
@section('title', 'New plan')

@section('content')
    <h1>New plan</h1>
    <div class="card">
        <form method="POST" action="{{ route('plans.store') }}">
            @csrf
            @include('admin.plans._form')
            <button class="btn">Create plan</button>
            <a class="btn secondary" href="{{ route('plans.index') }}">Cancel</a>
        </form>
    </div>
@endsection
