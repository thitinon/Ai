<?php

return [
    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
    ],
    'promptpay' => [
        'merchant_id' => env('PROMPTPAY_MERCHANT_ID'),
        'phone' => env('PROMPTPAY_PHONE'),
        'webhook_secret' => env('PROMPTPAY_WEBHOOK_SECRET'),
    ],
];
