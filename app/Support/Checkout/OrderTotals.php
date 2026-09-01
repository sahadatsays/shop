<?php

namespace App\Support\Checkout;

use App\Models\Discount;

class OrderTotals
{
    public function __construct(
        public readonly int $subtotalCents,
        public readonly int $discountCents,
        public readonly int $shippingCents,
        public readonly int $taxCents,
        public readonly int $totalCents,
        public readonly ?Discount $discount = null,
    ) {}

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
}
