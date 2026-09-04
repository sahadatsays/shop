<?php

namespace App\Support\Suppliers;

/**
 * Purchase aggregates for a supplier.
 *
 * Wired to Purchase Management later; currently returns empty/zero values so
 * supplier detail pages and future ledger/payment modules share one shape.
 */
final class SupplierPurchaseSummary
{
    /**
     * @param  list<array{product_id: int|null, name: string, quantity: int, total_cents: int}>  $productsPurchased
     */
    public function __construct(
        public readonly int $purchaseCount = 0,
        public readonly int $totalPurchaseValueCents = 0,
        public readonly int $outstandingPayableCents = 0,
        public readonly ?string $lastPurchaseAt = null,
        public readonly array $productsPurchased = [],
    ) {}

    public static function empty(): self
    {
        return new self;
    }
}
