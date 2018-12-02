<?php

return [

    'keys' => [

        'register_verification' => 'register.verification.',

    ],

    'expire_time' => [

        'register_verification' => env('EMAIL_REGISTER_VERIFICATION_TTL', 3600),

    ],

];
