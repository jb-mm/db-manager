<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | When true, the UI is available regardless of APP_ENV. When false, only
    | APP_ENV=local allows access (see middleware).
    |
    */
    'enabled' => env('DB_MANAGER_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    */
    'route_prefix' => env('DB_MANAGER_ROUTE_PREFIX', 'db-manager'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */
    'middleware' => ['web', 'db-manager.enabled'],

    /*
    |--------------------------------------------------------------------------
    | Database connection
    |--------------------------------------------------------------------------
    */
    'connection' => env('DB_MANAGER_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | UI theme
    |--------------------------------------------------------------------------
    |
    | auto — detect Tailwind first; else Bootstrap 5/6 from package.json semver.
    | tailwind | bootstrap5 | bootstrap6 — force a theme.
    | bootstrap — alias for bootstrap5 (backward compatible).
    |
    */
    'ui' => env('DB_MANAGER_UI', 'auto'),

    'supported_ui' => [
        'auto',
        'tailwind',
        'bootstrap',
        'bootstrap5',
        'bootstrap6',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets: project (host Vite) or cdn (explicit URLs below)
    |--------------------------------------------------------------------------
    */
    'assets' => env('DB_MANAGER_ASSETS', 'project'),

    'vite_entries' => [
        'resources/css/app.css',
    ],

    'tailwind_cdn_url' => env('DB_MANAGER_TAILWIND_CDN'),

    'bootstrap_cdn_urls' => [
        'bootstrap5' => env('DB_MANAGER_BOOTSTRAP5_CDN'),
        'bootstrap6' => env('DB_MANAGER_BOOTSTRAP6_CDN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Records per page
    |--------------------------------------------------------------------------
    */
    'per_page' => 25,

    'excluded_tables' => [
        'migrations',
        'password_reset_tokens',
        'personal_access_tokens',
        'failed_jobs',
        'job_batches',
        'cache',
        'cache_locks',
        'sessions',
    ],

    'read_only_tables' => [],

    'hidden_columns' => [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ],

];
