<?php

return [
    'points_per_dollar' => 1,

    'registration_bonus' => 250,

    'redemption' => [
        'points' => 100,
        'value_cents' => 500,
    ],

    'tiers' => [
        ['name' => 'Member', 'threshold' => 0],
        ['name' => 'Gold', 'threshold' => 1000],
        ['name' => 'Platinum', 'threshold' => 3000],
    ],
];
