<?php

namespace App\DTOs\Compare;

use App\Models\CompareItem;
use App\Models\Product;
use Illuminate\Support\Collection;

class CompareProductColumn
{
    /**
     * @param  Collection<int, array{name: string, value: string}>  $specifications
     */
    public function __construct(
        public CompareItem $compareItem,
        public Product $product,
        public string $name,
        public string $url,
        public ?string $image,
        public string $price,
        public ?string $oldPrice,
        public float $rating,
        public int $reviews,
        public string $availability,
        public bool $isLowStock,
        public ?string $brand,
        public ?string $category,
        public Collection $specifications,
        public string $materials,
        public string $care,
        public string $sizes,
        public ?string $warranty,
    ) {}

    public static function fromLineItem(CompareLineItem $line): self
    {
        $product = $line->product;

        return new self(
            compareItem: $line->compareItem,
            product: $product,
            name: $product->name,
            url: route('product.show', $product),
            image: $product->primaryImageUrl(),
            price: $product->formattedPrice(),
            oldPrice: $product->formattedCompareAtPrice(),
            rating: $product->placeholderRating(),
            reviews: $product->placeholderReviewCount(),
            availability: $product->stockStatusLabel(),
            isLowStock: $product->isLowStock(),
            brand: $product->brand?->name,
            category: $product->category?->name,
            specifications: $product->specifications->map(fn ($spec): array => [
                'name' => $spec->name,
                'value' => $spec->value,
            ]),
            materials: $product->materialAttributes()->pluck('value')->unique()->implode(' · '),
            care: $product->careAttributes()->pluck('value')->unique()->implode(' · '),
            sizes: $product->sizeAttributes()->pluck('value')->unique()->sort()->implode(', '),
            warranty: self::resolveWarranty($product),
        );
    }

    public function specificationValue(string $label): ?string
    {
        $normalized = strtolower($label);
        $spec = $this->specifications
            ->first(fn (array $spec): bool => strtolower($spec['name']) === $normalized);

        return $spec['value'] ?? null;
    }

    private static function resolveWarranty(Product $product): ?string
    {
        $warranty = $product->specifications
            ->first(fn ($spec): bool => str_contains(strtolower($spec->name), 'warranty'));

        return $warranty?->value;
    }
}
