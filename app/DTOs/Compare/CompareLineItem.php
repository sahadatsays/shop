<?php

namespace App\DTOs\Compare;

use App\Models\CompareItem;
use App\Models\Product;

class CompareLineItem
{
    public function __construct(
        public CompareItem $compareItem,
        public Product $product,
    ) {}
}
