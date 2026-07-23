<?php

namespace App\DTOs\Wishlist;

use App\Models\Wishlist;
use Illuminate\Support\Collection;

class WishlistSummary
{
    /**
     * @param  Collection<int, WishlistLineItem>  $items
     */
    public function __construct(
        public Wishlist $wishlist,
        public Collection $items,
        public int $itemCount,
    ) {}

    public function isEmpty(): bool
    {
        return $this->itemCount === 0;
    }

    /**
     * @return list<int>
     */
    public function productIds(): array
    {
        return $this->items
            ->map(fn (WishlistLineItem $line): int => $line->product->id)
            ->values()
            ->all();
    }
}
