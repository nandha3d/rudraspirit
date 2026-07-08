<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Response signing secret
    |--------------------------------------------------------------------------
    |
    | Shared secret used to HMAC-sign verification responses so clients can
    | confirm a response genuinely came from this server (and was not spoofed
    | by a proxy returning a fake "valid" answer). The SAME value must be set
    | as LICENSE_SIGNING_SECRET on every client deployment.
    |
    */
    'signing_secret' => env('LICENSE_SIGNING_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Default product slug
    |--------------------------------------------------------------------------
    |
    | Licenses issued without an explicit product default to this. Lets one
    | server license several products.
    |
    */
    'default_product' => env('LICENSE_DEFAULT_PRODUCT', 'rudraspirit-engine'),

    /*
    |--------------------------------------------------------------------------
    | Verify endpoint rate limit (requests / minute / IP)
    |--------------------------------------------------------------------------
    */
    'verify_rate_limit' => (int) env('LICENSE_VERIFY_RATE_LIMIT', 60),

];
