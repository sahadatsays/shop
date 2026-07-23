<?php

namespace App\Services\Admin;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaleProductService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()->with(['category', 'offers'])->published();

        if (($filters['on_sale'] ?? '1') === '1') {
            $query->onSale();
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(20)->withQueryString();
    }

    public function updatePricing(Product $product, array $data): Product
    {
        $priceCents = (int) round(((float) $data['price']) * 100);
        $compareAtCents = filled($data['compare_at_price'] ?? null)
            ? (int) round(((float) $data['compare_at_price']) * 100)
            : null;

        if ($compareAtCents !== null && $compareAtCents <= $priceCents) {
            $compareAtCents = null;
        }

        $product->update([
            'price_cents' => $priceCents,
            'compare_at_price_cents' => $compareAtCents,
        ]);

        return $product->fresh(['category', 'offers']);
    }
}
