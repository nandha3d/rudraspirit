@php
    // Shown only when licensing is enforced (warn/addons/admin) and the current
    // check is not valid. Never renders in 'off' mode. Cheap: reads the cached
    // verification result. Guarded so a licensing misconfig never breaks admin.
    $__license = null;
    try {
        $__licenseClient = app(\App\Services\License\LicenseClient::class);
        if (in_array($__licenseClient->enforceMode(), ['warn', 'addons', 'admin'], true)) {
            $__license = $__licenseClient->check();
        }
    } catch (\Throwable $e) {
        $__license = null;
    }
@endphp

@if ($__license !== null && ! ($__license['valid'] ?? false))
    <div class="alert alert-warning mt-3 mb-0" role="alert">
        <strong>{{ translate('License notice') }}:</strong>
        {{ translate('This deployment is not licensed') }}
        (<code>{{ $__license['status'] ?? 'invalid' }}</code>).
        @if ($__licenseClient->enforceMode() === 'addons')
            {{ translate('Add-on installation is disabled until a valid license is active.') }}
        @endif
        {{ translate('Set a valid LICENSE_KEY in your environment.') }}
    </div>
@endif
