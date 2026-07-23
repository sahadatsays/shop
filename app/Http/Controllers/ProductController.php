<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Services\CustomerAuthService;
use App\Services\ProductReviewService;
use App\Services\Storefront\ProductShowService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductShowService $productShow,
        private ProductReviewService $productReviews,
        private CustomerAuthService $customerAuth,
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

        $customer = $this->customerAuth->currentCustomer();
        $reviewSummary = $this->productReviews->summaryForProduct($product);
        $approvedReviews = $this->productReviews->approvedForProduct($product);

        return view('products.show', [
            'product' => $product,
            'breadcrumbs' => $this->productShow->breadcrumbs($product),
            'relatedProducts' => $this->productShow->relatedProducts($product),
            'recentlyViewedProducts' => $this->productShow->recentlyViewedProducts($product),
            'reviewSummary' => $reviewSummary,
            'approvedReviews' => $approvedReviews,
            'customer' => $customer,
            'canReview' => $customer ? $this->productReviews->canReview($customer, $product) : false,
            'hasReviewed' => $customer ? $this->productReviews->hasReviewed($customer, $product) : false,
            'customerReview' => $customer ? $this->productReviews->customerReviewForProduct($customer, $product) : null,
        ]);
    }
}
