<?php

return [

    'name' => env('APP_NAME', 'Laravel'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    'timezone' => 'Europe/Kiev',

    'locale' => 'uk',

    'fallback_locale' => 'en',

    'faker_locale' => 'uk_UA',

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'previous_keys' => array_filter(
        array_map('trim', explode(',', env('APP_PREVIOUS_KEYS', '')))
    ),

    'asu_key' => env('ASU_KEY'),
    'asu_host' => env('ASU_HOST'),
    'asu_key_scipub' => env('ASU_KEY_SCIPUB'),
    'cabinet_app_token' => env('CABINET_APP_TOKEN'),
    'cabinet_app_url' => env('CABINET_APP_URL'),
    'cabinet_app_service' => env('CABINET_APP_SERVICE', 'index/service/'),
    'protect_api_key' => env('PROTECT_API_KEY'),
    'protect_asu_api_key' => env('PROTECT_ASU_API_KEY'),

];