<?php

return [
    'free_shipping_threshold_cents' => 7500,
    'flat_shipping_cents' => 900,
    'tax_rate' => 0.08,
    'guest_expiry_days' => 30,
    'max_quantity_per_item' => 99,

    /*
    |--------------------------------------------------------------------------
    | Checkout shipping methods
    |--------------------------------------------------------------------------
    |
    | cost_cents null = use standard cart shipping rules (free over threshold,
    | otherwise flat_shipping_cents). Fixed values override that logic.
    |
    */
    'shipping_methods' => [
        'insideDhaka' => [
            'label' => 'Inside Dhaka',
            'description' => 'Arrives in 1–3 business days',
            'cost_cents' => 15000,
        ],
        'outsideDhaka' => [
            'label' => 'Outside Dhaka',
            'description' => 'Arrives in 3–5 business days',
            'cost_cents' => 15000,
        ]
    ],
];
