<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Store defaults (Bangladesh)
    |--------------------------------------------------------------------------
    */

    'currency' => env('STORE_CURRENCY', 'BDT'),
    'timezone' => env('STORE_TIMEZONE', 'Asia/Dhaka'),
    'country' => env('STORE_COUNTRY', 'Bangladesh'),
    'country_code' => env('STORE_COUNTRY_CODE', 'BD'),
    'phone_code' => env('STORE_PHONE_CODE', '+880'),
    'locale' => env('STORE_LOCALE', 'en_BD'),

    'name' => env('STORE_NAME', 'Jackpot BD LTD'),
    'tagline' => 'Quality products for every home across Bangladesh.',
    'description' => 'Jackpot BD LTD delivers trusted everyday products with reliable delivery inside and outside Dhaka.',
    'email' => env('STORE_EMAIL', 'hello@jackpotbd.com'),
    'support_email' => env('STORE_SUPPORT_EMAIL', 'support@jackpotbd.com'),
    'phone' => env('STORE_PHONE', '+8801710000000'),
    'address' => env('STORE_ADDRESS', 'House 12, Road 5, Dhanmondi, Dhaka 1209, Bangladesh'),

    'social_links' => [
        'facebook' => 'https://facebook.com/jackpotbd',
        'instagram' => 'https://instagram.com/jackpotbd',
        'youtube' => 'https://youtube.com/@jackpotbd',
    ],

    'mail_from_name' => env('MAIL_FROM_NAME', 'Jackpot BD LTD'),
    'mail_from_address' => env('MAIL_FROM_ADDRESS', 'noreply@jackpotbd.com'),

    'utility_bar_message' => 'Free delivery on orders over ৳2,000 • Cash on delivery available nationwide',

    // Whole taka amounts used when store settings values are empty.
    'free_shipping_threshold_amount' => 2000,
    'flat_shipping_amount' => 80,
    'inside_dhaka_shipping_amount' => 60,
    'outside_dhaka_shipping_amount' => 120,

    'meta_title' => 'Jackpot BD LTD — Online Shopping in Bangladesh',
    'meta_description' => 'Shop quality products from Jackpot BD LTD with delivery across Bangladesh and cash on delivery.',
];
