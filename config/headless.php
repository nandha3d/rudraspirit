<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Headless API V3 — Master Configuration
    |--------------------------------------------------------------------------
    |
    | Central configuration for the headless e-commerce engine.
    | All V3 API behavior is controlled from here.
    |
    */

    'enabled' => env('API_V3_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | CORS (Cross-Origin Resource Sharing)
    |--------------------------------------------------------------------------
    |
    | Allowed frontend origins. Use '*' for development.
    | In production, set API_CORS_ORIGINS to your frontend domain(s).
    | Multiple origins: "https://store.com,https://admin.store.com"
    |
    */

    'cors' => [
        'origins' => array_filter(
            explode(',', env('API_CORS_ORIGINS', '*'))
        ),
        'max_age' => 86400, // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Requests per minute for each tier.
    | Public = unauthenticated browsing (products, categories)
    | Auth   = logged-in customer actions (cart, orders)
    | Admin  = admin panel operations
    |
    */

    'rate_limits' => [
        'public' => (int) env('API_RATE_LIMIT_PUBLIC', 120),
        'auth'   => (int) env('API_RATE_LIMIT_AUTH', 60),
        'admin'  => (int) env('API_RATE_LIMIT_ADMIN', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default_per_page' => 20,
        'max_per_page'     => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    */

    'webhooks' => [
        'enabled'     => env('WEBHOOK_ENABLED', false),
        'secret'      => env('WEBHOOK_SECRET', ''),
        'max_retries' => 3,
        'retry_delays' => [10, 60, 300], // seconds between retries
    ],

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    */

    'version' => '3.0',

];
