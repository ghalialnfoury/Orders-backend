<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Origin',
    ],

    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    'supports_credentials' => false,

];
