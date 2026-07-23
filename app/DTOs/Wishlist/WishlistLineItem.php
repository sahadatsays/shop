<?php

namespace App\DTOs\Wishlist;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\WishlistItem;

class WishlistLineItem
{
    public function __construct(
        public WishlistItem $wishlistItem,
        public Product $product,
    ) {}

    public function availability(): string
    {
        if ($this->product->isOutOfStock()) {
            return 'out-of-stock';
        }

        if ($this->product->isLowStock()) {
            return 'low-stock';
        }

        return 'in-stock';
    }

    public function canMoveToCart(): bool
    {
        return $this->product->status === ProductStatus::Published
            && ! $this->product->trashed()
            && ! $this->product->isOutOfStock();
    }
}
