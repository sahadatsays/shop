<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ProductReviewService
{
    /**
     * @return array{count: int, average: float|null, distribution: array<int, int>}
     */
    public function summaryForProduct(Product $product): array
    {
        $ratings = Review::query()
            ->where('product_id', $product->id)
            ->approved()
            ->pluck('rating');

        $count = $ratings->count();
        $average = $count > 0 ? round((float) $ratings->avg(), 1) : null;

        $distribution = [];

        for ($stars = 5; $stars >= 1; $stars--) {
            $starCount = $ratings->filter(fn (int $rating): bool => $rating === $stars)->count();
            $distribution[$stars] = $count > 0 ? (int) round(($starCount / $count) * 100) : 0;
        }

        return [
            'count' => $count,
            'average' => $average,
            'distribution' => $distribution,
        ];
    }

    /**
     * @return Collection<int, Review>
     */
    public function approvedForProduct(Product $product, int $limit = 10): Collection
    {
        return Review::query()
            ->where('product_id', $product->id)
            ->approved()
            ->with(['customer'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Review>
     */
    public function forCustomer(Customer $customer): Collection
    {
        return Review::query()
            ->where('customer_id', $customer->id)
            ->whereNotNull('product_id')
            ->with(['product.category', 'product.images', 'order'])
            ->latest()
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    public function reviewableProducts(Customer $customer): Collection
    {
        $purchasedProductIds = $this->deliveredProductIds($customer);

        if ($purchasedProductIds->isEmpty()) {
            return collect();
        }

        $reviewedProductIds = Review::query()
            ->where('customer_id', $customer->id)
            ->whereIn('product_id', $purchasedProductIds)
            ->pluck('product_id');

        return Product::query()
            ->published()
            ->whereIn('id', $purchasedProductIds->diff($reviewedProductIds))
            ->with(['category', 'images'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{total: int, average: float|null}
     */
    public function customerStats(Customer $customer): array
    {
        $reviews = Review::query()
            ->where('customer_id', $customer->id)
            ->whereNotNull('product_id')
            ->get(['rating']);

        $total = $reviews->count();

        return [
            'total' => $total,
            'average' => $total > 0 ? round((float) $reviews->avg('rating'), 1) : null,
        ];
    }

    public function canReview(Customer $customer, Product $product): bool
    {
        if ($this->hasReviewed($customer, $product)) {
            return false;
        }

        return $this->eligibleOrder($customer, $product) !== null;
    }

    public function hasReviewed(Customer $customer, Product $product): bool
    {
        return Review::query()
            ->where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->exists();
    }

    public function customerReviewForProduct(Customer $customer, Product $product): ?Review
    {
        return Review::query()
            ->where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->first();
    }

    public function eligibleOrder(Customer $customer, Product $product): ?Order
    {
        return Order::query()
            ->where('customer_id', $customer->id)
            ->where('status', OrderStatus::Delivered)
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->latest('placed_at')
            ->first();
    }

    /**
     * @param  array{rating: int, title?: string|null, body: string}  $data
     */
    public function create(Customer $customer, Product $product, array $data): Review
    {
        $this->assertCanReview($customer, $product);

        $order = $this->eligibleOrder($customer, $product);

        if ($order === null) {
            throw ValidationException::withMessages([
                'product' => 'You can only review products from delivered orders.',
            ]);
        }

        return Review::query()->create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'author_name' => $customer->name,
            'rating' => (int) $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'is_approved' => (bool) config('shop.reviews_auto_approve', true),
        ]);
    }

    /**
     * @param  array{rating: int, title?: string|null, body: string}  $data
     */
    public function update(Customer $customer, Review $review, array $data): Review
    {
        $this->assertOwnedByCustomer($customer, $review);

        $review->update([
            'rating' => (int) $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
        ]);

        return $review->refresh();
    }

    public function delete(Customer $customer, Review $review): void
    {
        $this->assertOwnedByCustomer($customer, $review);

        $review->delete();
    }

    private function assertCanReview(Customer $customer, Product $product): void
    {
        if ($this->hasReviewed($customer, $product)) {
            throw ValidationException::withMessages([
                'product' => 'You have already reviewed this product.',
            ]);
        }

        if ($this->eligibleOrder($customer, $product) === null) {
            throw ValidationException::withMessages([
                'product' => 'You can only review products after your order has been delivered.',
            ]);
        }
    }

    private function assertOwnedByCustomer(Customer $customer, Review $review): void
    {
        if ($review->customer_id !== $customer->id) {
            abort(403);
        }

        if ($review->product_id === null) {
            abort(403);
        }
    }

    /**
     * @return Collection<int, int>
     */
    private function deliveredProductIds(Customer $customer): Collection
    {
        return Order::query()
            ->where('customer_id', $customer->id)
            ->where('status', OrderStatus::Delivered)
            ->with(['items:id,order_id,product_id'])
            ->get()
            ->flatMap(fn (Order $order) => $order->items->pluck('product_id'))
            ->unique()
            ->values();
    }
}
