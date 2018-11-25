<?php

return [

    'key' => env('JWT_KEY'),

    'ttl' => [
        'access_token' => env('JWT_ACCESS_TTL', 60 * 60 * 2),
        'refresh_token' => env('JWT_REFRESH_TTL', 60 * 60 * 24 * 10),
    ],

];
