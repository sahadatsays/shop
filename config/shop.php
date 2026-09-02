<?php

return [
    'require_in_stock' => env('SHOP_REQUIRE_IN_STOCK', true),

    'per_page_options' => [12, 24, 36, 48],

    'default_per_page' => 12,

    'default_sort' => 'featured',

    'category_cache_ttl' => 3600,

    'brand_cache_ttl' => 3600,

    'price_range_cache_ttl' => 3600,

    'placeholder_rating' => 4.7,

    'review_count_min' => 12,

    'review_count_max' => 150,

    'reviews_auto_approve' => env('SHOP_REVIEWS_AUTO_APPROVE', false),
];
