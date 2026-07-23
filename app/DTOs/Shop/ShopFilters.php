<?php

namespace App\DTOs\Shop;

use App\Enums\ProductSort;
use Illuminate\Http\Request;

class ShopFilters
{
    /**
     * @param  array<int, string>  $categories
     * @param  array<int, string>  $brands
     */
    public function __construct(
        public readonly ?string $search = null,
        public readonly array $categories = [],
        public readonly array $brands = [],
        public readonly ?int $minPriceCents = null,
        public readonly ?int $maxPriceCents = null,
        public readonly bool $inStock = true,
        public readonly bool $featured = false,
        public readonly bool $onSale = false,
        public readonly bool $newArrival = false,
        public readonly ProductSort $sort = ProductSort::Featured,
        public readonly int $perPage = 12,
        public readonly int $page = 1,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $validated = $request->validated();

        return new self(
            search: $validated['search'] ?? null,
            categories: self::normalizeSlugs($validated['category'] ?? []),
            brands: self::normalizeSlugs($validated['brand'] ?? []),
            minPriceCents: isset($validated['min_price']) ? (int) round((float) $validated['min_price'] * 100) : null,
            maxPriceCents: isset($validated['max_price']) ? (int) round((float) $validated['max_price'] * 100) : null,
            inStock: (bool) ($validated['in_stock'] ?? config('shop.require_in_stock', true)),
            featured: (bool) ($validated['featured'] ?? false),
            onSale: (bool) ($validated['on_sale'] ?? false),
            newArrival: (bool) ($validated['new_arrival'] ?? false),
            sort: ProductSort::tryFrom($validated['sort'] ?? config('shop.default_sort', 'featured')) ?? ProductSort::Featured,
            perPage: (int) ($validated['per_page'] ?? config('shop.default_per_page', 12)),
            page: max(1, (int) ($validated['page'] ?? 1)),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function queryParameters(): array
    {
        $params = array_filter([
            'search' => $this->search,
            'category' => $this->categories !== [] ? $this->categories : null,
            'brand' => $this->brands !== [] ? $this->brands : null,
            'min_price' => $this->minPriceCents !== null ? $this->minPriceCents / 100 : null,
            'max_price' => $this->maxPriceCents !== null ? $this->maxPriceCents / 100 : null,
            'in_stock' => $this->inStock ? null : '0',
            'featured' => $this->featured ? '1' : null,
            'on_sale' => $this->onSale ? '1' : null,
            'new_arrival' => $this->newArrival ? '1' : null,
            'sort' => $this->sort !== ProductSort::Featured ? $this->sort->value : null,
            'per_page' => $this->perPage !== (int) config('shop.default_per_page', 12) ? $this->perPage : null,
        ], fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []);

        return $params;
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== null
            || $this->categories !== []
            || $this->brands !== []
            || $this->minPriceCents !== null
            || $this->maxPriceCents !== null
            || ! $this->inStock
            || $this->featured
            || $this->onSale
            || $this->newArrival;
    }

    /**
     * @param  array<int, string>|string|null  $values
     * @return array<int, string>
     */
    private static function normalizeSlugs(array|string|null $values): array
    {
        if ($values === null) {
            return [];
        }

        $slugs = is_array($values) ? $values : [$values];

        return array_values(array_filter(array_map(
            fn (string $slug): string => trim($slug),
            $slugs,
        )));
    }
}
