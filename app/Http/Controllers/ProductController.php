<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Services\Storefront\ProductShowService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductShowService $productShow,
    ) {}

    public function show(?Product $product = null): View
    {
        $product ??= Product::query()->published()->ordered()->firstOrFail();

        abort_unless($product->status === ProductStatus::Published, 404);

        $product->load([
            'images',
            'category.parent',
            'brand',
            'specifications',
            'attributes',
            'relatedProducts.category',
            'relatedProducts.brand',
            'relatedProducts.images',
        ]);

        $this->productShow->trackRecentlyViewed($product);

        return view('products.show', [
            'product' => $product,
            'breadcrumbs' => $this->productShow->breadcrumbs($product),
            'relatedProducts' => $this->productShow->relatedProducts($product),
            'recentlyViewedProducts' => $this->productShow->recentlyViewedProducts($product),
        ]);
    }
}
