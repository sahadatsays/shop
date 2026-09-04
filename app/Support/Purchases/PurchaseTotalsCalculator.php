<?php

namespace App\Support\Purchases;

/**
 * Server-side purchase totals.
 *
 * Line: (qty * unit_cost) - line_discount + line_tax = line_subtotal
 * Header: sum(line_subtotals) - order_discount + shipping + tax = grand_total
 *
 * @phpstan-type LineInput array{
 *     quantity: int,
 *     unit_cost_cents: int,
 *     discount_cents?: int,
 *     tax_cents?: int
 * }
 */
final class PurchaseTotalsCalculator
{
    /**
     * @param  list<LineInput>  $lines
     * @return array{
     *     lines: list<array{
     *         quantity: int,
     *         unit_cost_cents: int,
     *         discount_cents: int,
     *         tax_cents: int,
     *         subtotal_cents: int
     *     }>,
     *     subtotal_cents: int,
     *     discount_cents: int,
     *     shipping_cents: int,
     *     tax_cents: int,
     *     grand_total_cents: int
     * }
     */
    public function calculate(
        array $lines,
        int $orderDiscountCents = 0,
        int $shippingCents = 0,
        int $orderTaxCents = 0,
    ): array {
        $normalizedLines = [];
        $subtotalCents = 0;

        foreach ($lines as $line) {
            $quantity = max(0, (int) ($line['quantity'] ?? 0));
            $unitCost = max(0, (int) ($line['unit_cost_cents'] ?? 0));
            $discount = max(0, (int) ($line['discount_cents'] ?? 0));
            $tax = max(0, (int) ($line['tax_cents'] ?? 0));
            $lineGross = $quantity * $unitCost;
            $lineSubtotal = max(0, $lineGross - $discount + $tax);

            $normalizedLines[] = [
                'quantity' => $quantity,
                'unit_cost_cents' => $unitCost,
                'discount_cents' => $discount,
                'tax_cents' => $tax,
                'subtotal_cents' => $lineSubtotal,
            ];

            $subtotalCents += $lineSubtotal;
        }

        $orderDiscountCents = max(0, $orderDiscountCents);
        $shippingCents = max(0, $shippingCents);
        $orderTaxCents = max(0, $orderTaxCents);

        $grandTotal = max(0, $subtotalCents - $orderDiscountCents + $shippingCents + $orderTaxCents);

        return [
            'lines' => $normalizedLines,
            'subtotal_cents' => $subtotalCents,
            'discount_cents' => $orderDiscountCents,
            'shipping_cents' => $shippingCents,
            'tax_cents' => $orderTaxCents,
            'grand_total_cents' => $grandTotal,
        ];
    }
}
