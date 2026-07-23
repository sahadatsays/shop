<?php

namespace App\DTOs\Cart;

use App\Models\CartItem;
use App\Models\Product;
use App\Support\MoneyFormatter;

class CartLineItem
{
    public function __construct(
        public readonly CartItem $cartItem,
        public readonly Product $product,
    ) {}

    public function lineTotalCents(): int
    {
        return $this->cartItem->lineTotalCents();
    }

    public function formattedLineTotal(): string
    {
        return MoneyFormatter::format($this->lineTotalCents());
    }

    public function formattedUnitPrice(): string
    {
        return MoneyFormatter::format($this->cartItem->unit_price_cents);
    }

    public function imageUrl(): ?string
    {
        return $this->product->primaryImageUrl();
    }

    public function productUrl(): string
    {
        return route('product.show', $this->product);
    }
}
