<?php

namespace App\Services\Storefront;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductShowService
{
    /**
     * @return list<array{label: string, url: string|null}>
     */
    public function breadcrumbs(Product $product): array
    {
        $crumbs = [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Shop', 'url' => route('shop')],
        ];

        $category = $product->category;

        if ($category !== null) {
            foreach ($category->breadcrumbTrail() as $ancestor) {
                $crumbs[] = [
                    'label' => $ancestor->name,
                    'url' => route('shop', ['category' => $ancestor->slug]),
                ];
            }
        }

        $crumbs[] = ['label' => $product->name, 'url' => null];

        return $crumbs;
    }

    /**
     * @return Collection<int, Product>
     */
    public function relatedProducts(Product $product): Collection
    {
        $limit = (int) config('product.related_products_limit', 4);

        $related = $product->relatedProducts()
            ->published()
            ->inStock()
            ->with(['category', 'brand', 'images'])
            ->limit($limit)
            ->get();

        if ($related->isNotEmpty()) {
            return $related;
        }

        if ($product->category_id === null) {
            return collect();
        }

        return Product::query()
            ->published()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with(['category', 'brand', 'images'])
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function trackRecentlyViewed(Product $product): void
    {
        $limit = (int) config('product.recently_viewed_limit', 8);

        /** @var list<int> $ids */
        $ids = session('recently_viewed_product_ids', []);

        $ids = array_values(array_filter($ids, fn (int $id): bool => $id !== $product->id));
        array_unshift($ids, $product->id);
        $ids = array_slice($ids, 0, $limit);

        session(['recently_viewed_product_ids' => $ids]);
    }

    /**
     * @return Collection<int, Product>
     */
    public function recentlyViewedProducts(Product $product): Collection
    {
        $displayLimit = (int) config('product.recently_viewed_display_limit', 4);

        /** @var list<int> $ids */
        $ids = array_values(array_filter(
            session('recently_viewed_product_ids', []),
            fn (int $id): bool => $id !== $product->id,
        ));

        if ($ids === []) {
            return collect();
        }

        $ids = array_slice($ids, 0, $displayLimit);

        return Product::query()
            ->published()
            ->inStock()
            ->whereIn('id', $ids)
            ->with(['category', 'brand', 'images'])
            ->get()
            ->sortBy(fn (Product $item): int => array_search($item->id, $ids, true) ?: 999)
            ->values();
    }
}
