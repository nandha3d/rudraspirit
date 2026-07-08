@extends('layouts.app')
@section('title', 'Licenses')

@section('content')
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
        <h1 style="margin:0;">Licenses</h1>
        <span class="spacer" style="flex:1;"></span>
        <a class="btn" href="{{ route('licenses.create') }}">+ New license</a>
    </div>

    <div class="card">
        <form method="GET" style="display:flex;gap:10px;margin-bottom:16px;">
            <input name="q" value="{{ request('q') }}" placeholder="Search key, name or email…" style="max-width:320px;">
            <select name="status" style="max-width:160px;">
                <option value="">All statuses</option>
                @foreach (['active','suspended','revoked'] as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn secondary">Filter</button>
        </form>

        <table>
            <tr><th>Key</th><th>Product</th><th>Customer</th><th>Status</th><th>Domains</th><th>Expires</th><th></th></tr>
            @forelse ($licenses as $l)
                <tr>
                    <td><a href="{{ route('licenses.show', $l) }}"><code>{{ $l->license_key }}</code></a></td>
                    <td class="muted">{{ $l->product }}</td>
                    <td>{{ $l->customer_name ?: '—' }}</td>
                    <td><span class="pill {{ $l->isExpired() ? 'expired' : $l->status }}">{{ $l->isExpired() ? 'expired' : $l->status }}</span></td>
                    <td>{{ $l->activations_count }} / {{ $l->activation_limit }}</td>
                    <td class="muted">{{ $l->expires_at ? $l->expires_at->toDateString() : 'perpetual' }}</td>
                    <td><a class="btn secondary sm" href="{{ route('licenses.edit', $l) }}">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No licenses found.</td></tr>
            @endforelse
        </table>

        <div style="margin-top:16px;">{{ $licenses->links() }}</div>
    </div>
@endsection
