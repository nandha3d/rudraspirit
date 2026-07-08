@extends('layouts.app')
@section('title', 'License ' . $license->license_key)

@section('content')
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
        <h1 style="margin:0;">License</h1>
        <span style="flex:1;"></span>
        <a class="btn secondary" href="{{ route('licenses.edit', $license) }}">Edit</a>
    </div>

    <div class="card">
        <div class="grid">
            <div><label>Key</label><div><code>{{ $license->license_key }}</code></div></div>
            <div><label>Product</label><div>{{ $license->product }}</div></div>
            <div><label>Status</label><div><span class="pill {{ $license->isExpired() ? 'expired' : $license->status }}">{{ $license->isExpired() ? 'expired' : $license->status }}</span></div></div>
            <div><label>Expires</label><div>{{ $license->expires_at ? $license->expires_at->toDateString() : 'perpetual' }}</div></div>
            <div><label>Customer</label><div>{{ $license->customer_name ?: '—' }}</div></div>
            <div><label>Email</label><div>{{ $license->customer_email ?: '—' }}</div></div>
            <div><label>Activation limit</label><div>{{ $license->activations->count() }} / {{ $license->activation_limit }} domains</div></div>
            <div><label>Plan</label><div>{{ $license->plan?->name ?: '— (manual)' }}</div></div>
        </div>
        @if ($license->notes)
            <div style="margin-top:14px;"><label>Notes</label><div class="muted">{{ $license->notes }}</div></div>
        @endif
    </div>

    <div class="card">
        <h2>Domain activations</h2>
        <table>
            <tr><th>Domain</th><th>IP</th><th>Activated</th><th>Last check</th><th></th></tr>
            @forelse ($license->activations as $a)
                <tr>
                    <td><code>{{ $a->domain }}</code></td>
                    <td class="muted">{{ $a->ip ?: '—' }}</td>
                    <td class="muted">{{ optional($a->activated_at)->diffForHumans() }}</td>
                    <td class="muted">{{ optional($a->last_check_at)->diffForHumans() }}</td>
                    <td>
                        <form method="POST" action="{{ route('licenses.activations.remove', [$license, $a]) }}"
                              onsubmit="return confirm('Release {{ $a->domain }}? It frees an activation slot.');">
                            @csrf @method('DELETE')
                            <button class="btn secondary sm">Release</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No domains have activated this license yet.</td></tr>
            @endforelse
        </table>
    </div>

    <div class="card">
        <h2>Addon entitlements</h2>
        @if ($license->plan && count($license->plan->moduleIdentifiers()))
            <p class="muted" style="margin-top:0;">
                From plan <strong>{{ $license->plan->name }}</strong>:
                @foreach ($license->plan->moduleIdentifiers() as $m) <code>{{ $m }}</code> @endforeach
                <br><small>Plan modules apply automatically; rows below are per-license extras.</small>
            </p>
        @endif
        <table>
            <tr><th>Identifier</th><th>Label</th><th>Expires</th><th></th></tr>
            @forelse ($license->addons as $addon)
                <tr>
                    <td><code>{{ $addon->addon_identifier }}</code></td>
                    <td>{{ $addon->label ?: '—' }}</td>
                    <td class="muted">{{ $addon->expires_at ? $addon->expires_at->toDateString() : 'with license' }}</td>
                    <td>
                        <form method="POST" action="{{ route('licenses.addons.remove', [$license, $addon]) }}">
                            @csrf @method('DELETE')
                            <button class="btn secondary sm">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">No addon entitlements. The base engine is licensed by the key itself.</td></tr>
            @endforelse
        </table>

        <form method="POST" action="{{ route('licenses.addons.add', $license) }}" style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
            @csrf
            <div class="field" style="margin:0;flex:1;min-width:180px;">
                <label>Addon identifier</label>
                <input name="addon_identifier" placeholder="e.g. affiliate_system" required>
            </div>
            <div class="field" style="margin:0;flex:1;min-width:150px;">
                <label>Label (optional)</label>
                <input name="label" placeholder="Affiliate System">
            </div>
            <div class="field" style="margin:0;">
                <label>Expires (optional)</label>
                <input type="date" name="expires_at">
            </div>
            <button class="btn">Add entitlement</button>
        </form>
    </div>
@endsection
