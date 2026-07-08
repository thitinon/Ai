<?php

return [
    'default' => env('MAIL_MAILER', 'mailhog'),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', config('app.name')),
    ],
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'localhost'),
            'port' => env('MAIL_PORT', 1025),
            'encryption' => env('MAIL_ENCRYPTION', null),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
        ],
        'mailhog' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'localhost'),
            'port' => env('MAIL_PORT', 1025),
        ],
    ],
];
