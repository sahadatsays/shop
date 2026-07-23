<?php

namespace App\DTOs\Admin\Dashboard;

readonly class LowStockProductData
{
    public function __construct(
        public int $productId,
        public string $name,
        public int $stockQuantity,
        public int $threshold,
    ) {}
}
