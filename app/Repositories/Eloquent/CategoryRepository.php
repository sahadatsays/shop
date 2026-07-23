<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function topByRevenue(int $limit = 6): Collection
    {
        return OrderItem::query()
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereNull('categories.deleted_at')
            ->select([
                'categories.name as label',
                DB::raw('SUM(order_items.line_total_cents) as revenue_cents'),
            ])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue_cents')
            ->limit($limit)
            ->get()
            ->map(fn ($row): array => [
                'label' => $row->label,
                'value' => (int) round($row->revenue_cents / 100),
            ]);
    }
}
