<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cart options
    |--------------------------------------------------------------------------
    |
    | Shipping charges come from Store Settings (admin). Method entries below
    | define labels/descriptions only.
    |
    */

    'tax_rate' => 0,
    'guest_expiry_days' => 30,
    'max_quantity_per_item' => 99,

    'shipping_methods' => [
        'insideDhaka' => [
            'label' => 'Inside Dhaka',
            'description' => 'Arrives in 1–3 business days',
        ],
        'outsideDhaka' => [
            'label' => 'Outside Dhaka',
            'description' => 'Arrives in 3–5 business days',
        ],
    ],
];
