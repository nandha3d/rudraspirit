@php
    $isEdit = $license->exists;
    $expires = old('expires_at', optional($license->expires_at)->format('Y-m-d'));
@endphp

<div class="grid">
    <div class="field">
        <label>Product</label>
        <input name="product" value="{{ old('product', $license->product) }}" required>
    </div>
    <div class="field">
        <label>Status</label>
        <select name="status">
            @foreach (['active','suspended','revoked'] as $s)
                <option value="{{ $s }}" @selected(old('status', $license->status)===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Plan (defines module entitlements)</label>
        <select name="plan_id">
            <option value="">— No plan (manual entitlements only) —</option>
            @foreach (\App\Models\Plan::orderBy('sort_order')->orderBy('price')->get() as $planOption)
                <option value="{{ $planOption->id }}" @selected((int) old('plan_id', $license->plan_id) === $planOption->id)>
                    {{ $planOption->name }} ({{ count($planOption->moduleIdentifiers()) }} modules)
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid">
    <div class="field">
        <label>Customer name</label>
        <input name="customer_name" value="{{ old('customer_name', $license->customer_name) }}">
    </div>
    <div class="field">
        <label>Customer email</label>
        <input type="email" name="customer_email" value="{{ old('customer_email', $license->customer_email) }}">
    </div>
</div>

<div class="grid">
    <div class="field">
        <label>Activation limit (max domains)</label>
        <input type="number" name="activation_limit" min="1" max="1000" value="{{ old('activation_limit', $license->activation_limit) }}" required>
    </div>
    <div class="field">
        <label>Expires at (blank = perpetual)</label>
        <input type="date" name="expires_at" value="{{ $expires }}">
    </div>
</div>

@unless ($isEdit)
    <div class="field">
        <label>License key (blank = auto-generate)</label>
        <input name="license_key" value="{{ old('license_key') }}" placeholder="Auto-generated if left empty">
    </div>
@endunless

<div class="field">
    <label>Notes (internal)</label>
    <textarea name="notes" rows="3">{{ old('notes', $license->notes) }}</textarea>
</div>
