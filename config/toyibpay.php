<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ToyibPay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */
    'secret_key' => env('TOYIBPAY_SECRET_KEY', '6bml69ib-lam3-v4mx-eurv-aw0edkso829f'),
    'category_code' => env('TOYIBPAY_CATEGORY_CODE', '5sfy663b'),
    'base_url' => env('TOYIBPAY_BASE_URL', 'https://toyyibpay.com'),
    'payment_channel' => env('TOYIBPAY_PAYMENT_CHANNEL', '2'), // 0=FPX, 1=Credit Card, 2=Both
];
