@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>

    <div class="card">
        <div class="grid">
            <div class="stat"><div class="n">{{ $stats['total'] }}</div><div class="l">Total licenses</div></div>
            <div class="stat"><div class="n" style="color:var(--ok)">{{ $stats['active'] }}</div><div class="l">Active</div></div>
            <div class="stat"><div class="n" style="color:var(--warn)">{{ $stats['suspended'] }}</div><div class="l">Suspended</div></div>
            <div class="stat"><div class="n" style="color:var(--bad)">{{ $stats['revoked'] }}</div><div class="l">Revoked</div></div>
            <div class="stat"><div class="n" style="color:var(--bad)">{{ $stats['expired'] }}</div><div class="l">Expired</div></div>
            <div class="stat"><div class="n">{{ $stats['activations'] }}</div><div class="l">Domain activations</div></div>
        </div>
    </div>

    <div style="display:flex;gap:20px;flex-wrap:wrap;">
        <div class="card" style="flex:1;min-width:320px;">
            <h2>Recent licenses</h2>
            <table>
                <tr><th>Key</th><th>Customer</th><th>Status</th></tr>
                @forelse ($recent as $l)
                    <tr>
                        <td><a href="{{ route('licenses.show', $l) }}"><code>{{ $l->license_key }}</code></a></td>
                        <td>{{ $l->customer_name ?: '—' }}</td>
                        <td><span class="pill {{ $l->status }}">{{ $l->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="muted">No licenses yet. <a href="{{ route('licenses.create') }}">Create one</a >.</td></tr>
                @endforelse
            </table>
        </div>

        <div class="card" style="flex:1;min-width:320px;">
            <h2>Expiring in 30 days</h2>
            <table>
                <tr><th>Key</th><th>Expires</th></tr>
                @forelse ($expiringSoon as $l)
                    <tr>
                        <td><a href="{{ route('licenses.show', $l) }}"><code>{{ $l->license_key }}</code></a></td>
                        <td>{{ $l->expires_at->toDateString() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="muted">Nothing expiring soon.</td></tr>
                @endforelse
            </table>
        </div>
    </div>

    <p><a class="btn" href="{{ route('licenses.create') }}">+ New license</a></p>
@endsection
