<?php

namespace App\DTOs\Compare;

use App\Models\CompareList;
use Illuminate\Support\Collection;

class CompareSummary
{
    /**
     * @param  Collection<int, CompareLineItem>  $items
     */
    public function __construct(
        public CompareList $compareList,
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
            ->map(fn (CompareLineItem $line): int => $line->product->id)
            ->values()
            ->all();
    }
}
