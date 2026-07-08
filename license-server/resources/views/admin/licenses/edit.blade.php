@extends('layouts.app')
@section('title', 'Edit license')

@section('content')
    <div style="display:flex;align-items:center;gap:12px;">
        <h1 style="margin:0 0 18px;">Edit license</h1>
        <span style="flex:1;"></span>
        <a href="{{ route('licenses.show', $license) }}">View →</a>
    </div>
    <div class="card">
        <p><label>License key</label> <code>{{ $license->license_key }}</code></p>
        <form method="POST" action="{{ route('licenses.update', $license) }}">
            @csrf @method('PUT')
            @include('admin.licenses._form')
            <button class="btn">Save changes</button>
            <a class="btn secondary" href="{{ route('licenses.index') }}">Cancel</a>
        </form>
    </div>

    <div class="card">
        <h2>Danger zone</h2>
        <form method="POST" action="{{ route('licenses.destroy', $license) }}"
              onsubmit="return confirm('Delete this license and all its activations? This cannot be undone.');">
            @csrf @method('DELETE')
            <button class="btn danger">Delete license</button>
        </form>
    </div>
@endsection
