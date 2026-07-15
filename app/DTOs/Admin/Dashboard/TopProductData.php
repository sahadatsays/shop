<?php

namespace App\DTOs\Admin\Dashboard;

readonly class TopProductData
{
    public function __construct(
        public string $name,
        public string $category,
        public int $unitsSold,
        public string $revenueFormatted,
    ) {}
}
