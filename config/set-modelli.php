<?php

return [
    'login_api' => [
        'url' => env('API_LOGIN_URL'),
        'shared_secret' => env('API_LOGIN_SHARED_SECRET'),
        'app_name' => env('API_LOGIN_APP_NAME', 'set_modelli_lotti'),
        'timeout' => env('API_LOGIN_TIMEOUT', 10),
        'verify_ssl' => filter_var(env('API_LOGIN_VERIFY_SSL', true), FILTER_VALIDATE_BOOL),
    ],
];