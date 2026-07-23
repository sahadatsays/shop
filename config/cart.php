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
        'standard' => [
            'label' => 'Standard shipping',
            'description' => 'Arrives in 5–7 business days',
            'cost_cents' => null,
        ],
        'express' => [
            'label' => 'Express shipping',
            'description' => 'Arrives in 2–3 business days',
            'cost_cents' => 1200,
        ],
        'overnight' => [
            'label' => 'Overnight shipping',
            'description' => 'Next business day by 5 PM',
            'cost_cents' => 2400,
        ],
    ],
];
