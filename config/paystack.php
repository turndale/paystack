<?php

return [
    'secret' => env('PAYSTACK_SECRET_KEY'),
    'public' => env('PAYSTACK_PUBLIC_KEY'),
    'url' => env('PAYSTACK_URL', 'https://api.paystack.co'),
    'callback_url' => env('PAYSTACK_CALLBACK_URL', 'https://website.com/paystack/callback'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Webhook Whitelisted IPs
    |--------------------------------------------------------------------------
    |
    | Paystack currently sends webhooks from these specific IP addresses.
    | You can override these in your .env if they change in the future.
    |
    */
    'white_listed_ips' => [
        env('PAYSTACK_WHITE_LISTED_IP_1', '52.31.139.75'),
        env('PAYSTACK_WHITE_LISTED_IP_2', '52.49.173.169'),
        env('PAYSTACK_WHITE_LISTED_IP_3', '52.214.14.220'),
        env('PAYSTACK_WHITE_LISTED_IP_4', 'update_if exist at https://paystack.com/docs/payments/webhooks/#ip-whitelisting'), 
    ],
];