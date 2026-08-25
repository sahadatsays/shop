<?php

namespace App\Services;

use App\DTOs\Shop\ShopFilters;
use App\Enums\ProductSort;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\HomepageSettings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchService
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function search(?string $query, int $page = 1, int $perPage = 12): LengthAwarePaginator
    {
        $filters = new ShopFilters(
            search: filled($query) ? trim($query) : null,
            sort: ProductSort::Featured,
            perPage: $perPage,
            page: $page,
        );

        return $this->productService->getFilteredProducts($filters);
    }

    /**
     * @return array{products: list<array<string, mixed>>, categories: list<array<string, mixed>>, brands: list<array<string, mixed>>}
     */
    public function suggestions(string $query, int $limit = 6): array
    {
        $term = trim($query);

        if ($term === '') {
            return [
                'products' => [],
                'categories' => [],
                'brands' => [],
            ];
        }

        $like = '%' . $term . '%';

        $products = Product::query()
            ->published()
            ->whereNotNull('category_id')
            ->where(function ($builder) use ($like): void {
                $builder->where('name', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhereHas('brand', fn($brand) => $brand->where('name', 'like', $like))
                    ->orWhereHas('category', fn($category) => $category->where('name', 'like', $like));
            })
            ->with(['category', 'images'])
            ->limit($limit)
            ->get()
            ->map(fn(Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->formattedPrice(),
                'image' => $product->primaryImageUrl(),
                'url' => route('product.show', $product),
                'category' => $product->category?->name,
            ])
            ->values()
            ->all();

        $categories = Category::query()
            ->active()
            ->where('name', 'like', $like)
            ->ordered()
            ->limit(4)
            ->get(['id', 'name', 'slug'])
            ->map(fn(Category $category): array => [
                'name' => $category->name,
                'url' => route('shop', ['category' => [$category->slug]]),
            ])
            ->all();

        $brands = Brand::query()
            ->active()
            ->where('name', 'like', $like)
            ->ordered()
            ->limit(4)
            ->get(['id', 'name', 'slug'])
            ->map(fn(Brand $brand): array => [
                'name' => $brand->name,
                'url' => route('shop', ['brand' => [$brand->slug]]),
            ])
            ->all();

        return [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ];
    }

    /**
     * @return list<string>
     */
    public function popularSearches(): array
    {
        return HomepageSettings::current()->popular_searches
            ?? ['flags', 'challenge coins', 'boots', 'packs'];
    }
}
