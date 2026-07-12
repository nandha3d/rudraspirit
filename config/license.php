<?php

return [
    /*
    |--------------------------------------------------------------------------
    | License / activation server
    |--------------------------------------------------------------------------
    | Base URL of the license server that issues and verifies purchase codes.
    | The addon installer validates the domain + addon purchase code against it.
    | Deploy the app in /license-server to this host.
    */
    'server_url' => rtrim(env('LICENSE_SERVER_URL', 'https://license.animazon.in'), '/'),

    // Master switch for the addon activation check. Set false to skip the
    // remote check entirely (useful for self-hosted / offline installs).
    'enabled' => filter_var(env('LICENSE_CHECK_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
];
