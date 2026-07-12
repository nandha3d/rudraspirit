<?php
/**
 * License server configuration.
 *
 * Override any value with an environment variable of the same name (set them in
 * Hostinger hPanel → Advanced → PHP Configuration, or a .env style include).
 */

return [
    // Admin panel access token. CHANGE THIS before deploying.
    // Log in at /admin with this token.
    'admin_token' => getenv('LICENSE_ADMIN_TOKEN') ?: 'CHANGE-ME-super-secret-admin-token',

    // Storage. 'sqlite' needs no setup (recommended for shared hosting).
    // Use 'mysql' to point at a Hostinger MySQL database instead.
    'db_driver' => getenv('LICENSE_DB_DRIVER') ?: 'sqlite',

    // sqlite: absolute path to the database file (kept outside the web root by
    // default so it can never be downloaded).
    'sqlite_path' => getenv('LICENSE_SQLITE_PATH') ?: __DIR__ . '/data/licenses.sqlite',

    // mysql: used only when db_driver = mysql
    'mysql' => [
        'host' => getenv('LICENSE_DB_HOST') ?: '127.0.0.1',
        'port' => getenv('LICENSE_DB_PORT') ?: '3306',
        'database' => getenv('LICENSE_DB_NAME') ?: 'license',
        'username' => getenv('LICENSE_DB_USER') ?: 'root',
        'password' => getenv('LICENSE_DB_PASS') ?: '',
    ],

    // Prefix for generated purchase codes.
    'code_prefix' => getenv('LICENSE_CODE_PREFIX') ?: 'RS',

    // Default number of distinct domains a single code may activate on.
    'default_max_domains' => (int) (getenv('LICENSE_DEFAULT_MAX_DOMAINS') ?: 1),

    // Product/brand name shown in the admin UI.
    'brand' => getenv('LICENSE_BRAND') ?: 'Animazon License Server',
];
