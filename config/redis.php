<?php

return [

    'keys' => [

        'last_logged_in_ip' => 'account.ip.',

        'register_verification' => 'register.verification.',

        'tokens' => 'token.',

    ],

    'expire_time' => [

        'register_verification' => env('EMAIL_REGISTER_VERIFICATION_TTL', 3600),

    ],

];
