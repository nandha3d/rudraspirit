@extends('layouts.app')
@section('title', 'Edit plan')

@section('content')
    <h1>Edit plan — {{ $plan->name }}</h1>
    <div class="card">
        <form method="POST" action="{{ route('plans.update', $plan) }}">
            @csrf @method('PUT')
            @include('admin.plans._form')
            <button class="btn">Save changes</button>
            <a class="btn secondary" href="{{ route('plans.index') }}">Cancel</a>
        </form>
    </div>
@endsection
