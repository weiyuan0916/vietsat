<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | External Service API
    |--------------------------------------------------------------------------
    |
    | Configuration for external service API integration.
    | This is used to fetch services from an external provider.
    |
    */

    'external_api' => [
        'base_url' => env('EXTERNAL_SERVICE_API_URL', 'https://tiemnhaduy.com/api/v1'),
        'cache_ttl' => env('EXTERNAL_SERVICE_API_CACHE_TTL', 300), // 5 minutes
        'timeout' => env('EXTERNAL_SERVICE_API_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for local service management.
    | Set use_external_api to true to fetch services from external API.
    |
    */

    'service' => [
        'use_external_api' => env('USE_EXTERNAL_SERVICE_API', true),
    ],

];
