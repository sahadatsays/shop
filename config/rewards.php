<?php

return [
    'points_per_taka' => 1,

    'registration_bonus' => 250,

    'redemption' => [
        'points' => 100,
        // Whole taka amount awarded when redeeming the points package.
        'value_amount' => 5,
    ],

    'tiers' => [
        ['name' => 'Member', 'threshold' => 0],
        ['name' => 'Gold', 'threshold' => 1000],
        ['name' => 'Platinum', 'threshold' => 3000],
    ],
];
