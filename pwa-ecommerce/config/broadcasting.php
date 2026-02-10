<?php

return [
    'default' => env('BROADCAST_CONNECTION', 'reverb'),

    'connections' => [
        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY', 'vietsat_reverb_key'),
            'secret' => env('REVERB_APP_SECRET', 'vietsat_reverb_secret'),
            'app_id' => env('REVERB_APP_ID', 'vietsat_app'),
            'options' => [
                'host' => env('REVERB_HOST', '127.0.0.1'),
                'port' => env('REVERB_PORT', 8080),
                'scheme' => env('REVERB_SCHEME', 'http'),
                'useTLS' => false,
            ],
            'client' => [
                'max_message_size' => 10 * 1024, // 10KB
            ],
            'scaling' => [
                'enabled' => false,
                'max_connection_count' => 10,
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                'host' => env('PUSHER_HOST') ?: null,
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
            ],
            'client' => [
                'max_message_size' => 10 * 1024, // 10KB
            ],
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
