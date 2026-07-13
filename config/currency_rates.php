<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Live currency exchange rate updater
    |--------------------------------------------------------------------------
    |
    | Rates are fetched relative to the system default currency and written to
    | each active currency's exchange_rate. convert_price() uses rate ratios,
    | so the default currency is always anchored at 1.
    |
    | Default provider: open.er-api.com (free, no API key, daily updates).
    | The base currency code is appended to provider_url, e.g.
    |   https://open.er-api.com/v6/latest/INR
    |
    */

    'enabled'      => env('CURRENCY_AUTO_UPDATE', true),

    'provider_url' => env('CURRENCY_RATE_PROVIDER_URL', 'https://open.er-api.com/v6/latest'),

    'timeout'      => env('CURRENCY_RATE_TIMEOUT', 15),
];
