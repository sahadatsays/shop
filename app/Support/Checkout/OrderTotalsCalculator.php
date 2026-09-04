<?php

namespace App\Support\Checkout;

use App\Models\Discount;

class OrderTotalsCalculator
{
    public function calculate(
        int $subtotalCents,
        int $shippingCents,
        ?Discount $discount = null,
    ): OrderTotals {
        $discountCents = 0;

        if ($discount instanceof Discount && $discount->isAvailable()) {
            $discountCents = $discount->discountAmountCents($subtotalCents);
        }

        return $this->calculateFromAmounts($subtotalCents, $discountCents, $shippingCents, $discount);
    }

    public function calculateFromAmounts(
        int $subtotalCents,
        int $discountCents,
        int $shippingCents,
        ?Discount $discount = null,
    ): OrderTotals {
        $discountCents = max(0, min($discountCents, $subtotalCents));
        $taxableCents = max(0, $subtotalCents - $discountCents);
        $taxRate = (float) config('cart.tax_rate', 0);
        $taxCents = (int) round($taxableCents * $taxRate);
        $totalCents = $taxableCents + $shippingCents + $taxCents;

        return new OrderTotals(
            subtotalCents: $subtotalCents,
            discountCents: $discountCents,
            shippingCents: $shippingCents,
            taxCents: $taxCents,
            totalCents: $totalCents,
            discount: $discountCents > 0 ? $discount : null,
        );
    }
}
