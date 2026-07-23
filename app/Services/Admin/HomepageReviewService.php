<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\Review;
use App\Support\HomepageSettings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HomepageReviewService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Review::query()->with('product');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('author_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if (($filters['status'] ?? null) === 'approved') {
            $query->where('is_approved', true);
        }

        if (($filters['status'] ?? null) === 'pending') {
            $query->where('is_approved', false);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    /**
     * @return Collection<int, Product>
     */
    public function productOptions(): Collection
    {
        return Product::query()
            ->published()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function find(int $id): Review
    {
        return Review::query()->with('product')->findOrFail($id);
    }

    public function create(array $data): Review
    {
        $review = Review::query()->create($this->prepareAttributes($data));
        HomepageSettings::clearCache();

        return $review;
    }

    public function update(Review $review, array $data): Review
    {
        $review->update($this->prepareAttributes($data));
        HomepageSettings::clearCache();

        return $review->refresh();
    }

    public function delete(Review $review): void
    {
        $review->delete();
        HomepageSettings::clearCache();
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data): array
    {
        return [
            'product_id' => $data['product_id'] ?? null,
            'author_name' => $data['author_name'],
            'rating' => (int) $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'is_approved' => (bool) ($data['is_approved'] ?? false),
        ];
    }
}
