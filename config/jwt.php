<?php

return [

    'key' => env('JWT_KEY'),

    'ttl' => [
        'access_token' => env('JWT_ACCESS_TTL', 60 * 60 * 2), /* default at 2 hours */
        'refresh_token' => env('JWT_REFRESH_TTL', 60 * 60 * 24 * 10), /* default at 10 days */
    ],

];
