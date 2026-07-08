@php
    $modulesText  = old('modules_text', implode("\n", $plan->moduleIdentifiers()));
    $featuresText = old('features_text', implode("\n", $plan->features ?? []));
@endphp

<div class="grid">
    <div class="field">
        <label>Plan name</label>
        <input name="name" value="{{ old('name', $plan->name) }}" required>
    </div>
    <div class="field">
        <label>Sort order (lower = first)</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $plan->sort_order) }}">
    </div>
</div>

<div class="field">
    <label>Description (shown on the pricing card)</label>
    <textarea name="description" rows="2">{{ old('description', $plan->description) }}</textarea>
</div>

<div class="grid">
    <div class="field">
        <label>Price</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $plan->price) }}" required>
    </div>
    <div class="field">
        <label>Currency</label>
        <input name="currency" value="{{ old('currency', $plan->currency) }}" required>
    </div>
    <div class="field">
        <label>Billing period</label>
        <select name="billing_period">
            @foreach (['monthly','yearly','lifetime'] as $p)
                <option value="{{ $p }}" @selected(old('billing_period', $plan->billing_period)===$p)>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid">
    <div class="field">
        <label>License validity in days (blank = perpetual)</label>
        <input type="number" name="duration_days" min="1" value="{{ old('duration_days', $plan->duration_days) }}">
    </div>
    <div class="field">
        <label>Activation limit (max domains)</label>
        <input type="number" name="activation_limit" min="1" max="1000" value="{{ old('activation_limit', $plan->activation_limit) }}" required>
    </div>
</div>

<div class="field">
    <label>Modules included — one addon identifier per line (e.g. affiliate_system, auction, club_point, otp_system, refund_request, seller_subscription, wholesale, pos_system, offline_payment)</label>
    <textarea name="modules_text" rows="5" placeholder="affiliate_system&#10;club_point">{{ $modulesText }}</textarea>
</div>

<div class="field">
    <label>Feature bullets for the pricing card — one per line</label>
    <textarea name="features_text" rows="5" placeholder="Unlimited products&#10;Priority support">{{ $featuresText }}</textarea>
</div>

<div class="field">
    <label>External payment link (optional — customer is redirected here after checkout)</label>
    <input name="payment_link" value="{{ old('payment_link', $plan->payment_link) }}" placeholder="https://rzp.io/l/your-payment-page">
</div>

<div class="grid">
    <div class="field">
        <label style="display:flex;align-items:center;gap:8px;color:var(--fg);">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" style="width:auto;" @checked(old('is_active', $plan->is_active))>
            Active (visible on pricing page)
        </label>
    </div>
    <div class="field">
        <label style="display:flex;align-items:center;gap:8px;color:var(--fg);">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1" style="width:auto;" @checked(old('is_featured', $plan->is_featured))>
            Featured (highlighted card)
        </label>
    </div>
</div>
