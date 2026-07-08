<?php

/*
|--------------------------------------------------------------------------
| License client configuration
|--------------------------------------------------------------------------
|
| This deployment validates itself against a license server (see the
| license-server/ project). All values come from the environment so the
| same codebase can be licensed per-deployment.
|
*/

return [

    // Full base URL of the license server.
    'server_url' => env('LICENSE_SERVER_URL', 'https://license.animazon.in'),

    // The product slug this deployment licenses.
    'product' => env('LICENSE_PRODUCT', 'animazon-engine'),

    // This deployment's license key.
    'key' => env('LICENSE_KEY', ''),

    // The domain to license under. Falls back to the APP_URL host.
    'domain' => env('LICENSE_DOMAIN', ''),

    // Shared HMAC secret — MUST match the license server's LICENSE_SIGNING_SECRET.
    // Used to verify that a "valid" response genuinely came from the server.
    'signing_secret' => env('LICENSE_SIGNING_SECRET', ''),

    // How long (minutes) to cache a verification result before re-checking.
    'cache_ttl' => (int) env('LICENSE_CACHE_TTL', 720), // 12 hours

    // HTTP timeout (seconds) when contacting the license server.
    'timeout' => (int) env('LICENSE_TIMEOUT', 8),

    /*
    | Enforcement mode — what happens when the license is missing/invalid:
    |
    |   'off'     : licensing disabled entirely (no checks run). Default, so an
    |               unconfigured install is never accidentally bricked.
    |   'warn'    : storefront + admin work normally; admin sees a warning banner.
    |   'addons'  : as 'warn', PLUS new addon installs are blocked. Storefront
    |               and existing features keep working.
    |   'admin'   : as 'addons', PLUS the admin panel is blocked (storefront stays
    |               up so customers are never affected).
    |
    | The storefront is never taken down by licensing — that is deliberate.
    */
    'enforce' => env('LICENSE_ENFORCE', 'off'),

    /*
    | Fail-open vs fail-closed when the license server is UNREACHABLE (network
    | error / timeout) and there is no cached answer:
    |   true  = treat as licensed (fail-open) — safest for uptime.
    |   false = treat as unlicensed (fail-closed) — strictest.
    | Note: a definitive "invalid" answer is always honored regardless of this.
    */
    'fail_open' => (bool) env('LICENSE_FAIL_OPEN', true),

];
