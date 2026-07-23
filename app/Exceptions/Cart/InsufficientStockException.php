<?php

namespace App\Exceptions\Cart;

use App\Models\Product;
use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(
        public readonly Product $product,
        public readonly int $available,
        public readonly int $requested,
    ) {
        parent::__construct(
            "{$product->name} only has {$available} in stock (requested {$requested}).",
        );
    }
}
