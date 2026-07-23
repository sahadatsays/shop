<?php

namespace App\DTOs\Cart;

use App\Models\Cart;
use App\Support\MoneyFormatter;
use Illuminate\Support\Collection;

class CartSummary
{
    /**
     * @param  Collection<int, CartLineItem>  $items
     */
    public function __construct(
        public readonly Cart $cart,
        public readonly Collection $items,
        public readonly int $itemCount,
        public readonly int $subtotalCents,
        public readonly int $shippingCents,
        public readonly int $taxCents,
        public readonly int $totalCents,
    ) {}

    public function formattedSubtotal(): string
    {
        return MoneyFormatter::format($this->subtotalCents);
    }

    public function formattedShipping(): string
    {
        return $this->shippingCents === 0 ? 'Free' : MoneyFormatter::format($this->shippingCents);
    }

    public function formattedTax(): string
    {
        return MoneyFormatter::format($this->taxCents);
    }

    public function formattedTotal(): string
    {
        return MoneyFormatter::format($this->totalCents);
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    public function qualifiesForFreeShipping(): bool
    {
        return $this->subtotalCents >= (int) config('cart.free_shipping_threshold_cents', 7500);
    }
}
