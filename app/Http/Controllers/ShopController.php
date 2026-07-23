<?php

namespace App\Http\Controllers;

use App\DTOs\Shop\ShopFilters;
use App\Enums\ProductSort;
use App\Http\Requests\ShopIndexRequest;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        private ProductService $products,
    ) {}

    public function index(ShopIndexRequest $request): View
    {
        $filters = ShopFilters::fromRequest($request);

        $facetFilters = new ShopFilters(
            search: $filters->search,
            categories: [],
            brands: [],
            minPriceCents: null,
            maxPriceCents: null,
            inStock: $filters->inStock,
            featured: false,
            onSale: false,
            newArrival: false,
            sort: $filters->sort,
            perPage: $filters->perPage,
        );

        $products = $this->products->getFilteredProducts($filters);
        $categories = $this->products->getCategories($facetFilters);
        $brands = $this->products->getBrands($facetFilters);
        $priceRange = $this->products->getPriceRange($facetFilters);
        $activeFilterLabels = $this->products->activeFilterLabels($filters);

        $currentCategory = $filters->categories !== []
            ? Category::query()->whereIn('slug', $filters->categories)->ordered()->pluck('name')->implode(', ')
            : null;

        return view('shop', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories,
            'brands' => $brands,
            'priceRange' => $priceRange,
            'activeFilterLabels' => $activeFilterLabels,
            'currentCategory' => $currentCategory,
            'sortOptions' => ProductSort::cases(),
            'perPageOptions' => config('shop.per_page_options', [12, 24, 36, 48]),
        ]);
    }
}
