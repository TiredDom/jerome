<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => env('PUSHER_APP_USE_TLS', false),
                'host' => env('PUSHER_APP_HOST', null),
                'port' => env('PUSHER_APP_PORT', null),
                'scheme' => env('PUSHER_APP_SCHEME', null),
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
    ],
];
