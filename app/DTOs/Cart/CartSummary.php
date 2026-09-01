<?php

namespace App\DTOs\Cart;

use App\Models\Cart;
use App\Models\Discount;
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
        public readonly int $discountCents,
        public readonly int $shippingCents,
        public readonly int $taxCents,
        public readonly int $totalCents,
        public readonly ?Discount $discount = null,
    ) {}

    public function formattedSubtotal(): string
    {
        return MoneyFormatter::format($this->subtotalCents);
    }

    public function formattedDiscount(): string
    {
        return '−'.MoneyFormatter::format($this->discountCents);
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

    public function hasDiscount(): bool
    {
        return $this->discountCents > 0;
    }

    public function discountLabel(): ?string
    {
        if (! $this->discount instanceof Discount) {
            return null;
        }

        return $this->discount->code.' — '.$this->discount->formattedValue().' off';
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
