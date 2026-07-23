<?php

namespace App\Services\Admin\Dashboard;

use App\Enums\OrderStatus;
use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\NewsletterSubscriber;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;
use App\Support\Dashboard\WidgetContext;
use App\Support\MoneyFormatter;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Central source of truth for every dashboard figure.
 *
 * Business rules enforced here:
 *  - Revenue counts ONLY completed (delivered) orders.
 *  - Pending / cancelled / returned / refunded orders never inflate revenue.
 *  - Inventory value is derived live from stock x price.
 *  - Best-sellers & top customers come from completed orders only.
 *  - Every range-sensitive figure respects the selected global date filter and
 *    is compared against the immediately preceding equal-length window.
 */
class DashboardMetrics
{
    /**
     * Order statuses that realise revenue.
     *
     * @return array<int, OrderStatus>
     */
    public static function revenueStatuses(): array
    {
        return [OrderStatus::Delivered];
    }

    // ---------------------------------------------------------------------
    // Statistic groups
    // ---------------------------------------------------------------------

    /**
     * @return array<int, array<string, mixed>>
     */
    public function salesStats(WidgetContext $context): array
    {
        [$prevStart, $prevEnd] = $this->previousBounds($context);

        $revenue = $this->completedRevenueCents($context->start, $context->end);
        $prevRevenue = $this->completedRevenueCents($prevStart, $prevEnd);

        $orders = $this->ordersPlaced($context->start, $context->end);
        $prevOrders = $this->ordersPlaced($prevStart, $prevEnd);

        $aov = $this->averageOrderValueCents($context->start, $context->end);
        $prevAov = $this->averageOrderValueCents($prevStart, $prevEnd);

        $todaySales = $this->completedRevenueCents(
            CarbonImmutable::now()->startOfDay(),
            CarbonImmutable::now()->endOfDay(),
        );

        return [
            $this->metric('Completed Revenue', MoneyFormatter::formatCompact($revenue), $revenue, $prevRevenue, 'M3 3v18h18M7 16l4-4 4 4 5-6'),
            $this->metric('Orders', (string) $orders, $orders, $prevOrders, 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z'),
            $this->metric('Avg. Order Value', MoneyFormatter::format($aov), $aov, $prevAov, 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'),
            $this->pointMetric("Today's Sales", MoneyFormatter::format($todaySales), $todaySales > 0 ? 'up' : 'neutral', 'Completed today', 'M12 8v8m-4-4h8M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function orderStats(WidgetContext $context): array
    {
        $counts = $this->ordersCountByStatus($context->start, $context->end);

        $pending = collect(OrderStatus::pendingStatuses())
            ->sum(fn (OrderStatus $status): int => (int) ($counts[$status->value] ?? 0));

        return [
            $this->pointMetric('Pending', (string) $pending, $pending > 0 ? 'up' : 'neutral', 'Awaiting fulfillment', 'M12 8v4l3 3M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z'),
            $this->pointMetric('Shipped', (string) ($counts[OrderStatus::Shipped->value] ?? 0), 'neutral', 'In transit', 'M3 7h11v8H3zM14 10h4l3 3v2h-7'),
            $this->pointMetric('Delivered', (string) ($counts[OrderStatus::Delivered->value] ?? 0), 'up', 'Completed', 'M20 6 9 17l-5-5'),
            $this->pointMetric('Cancelled', (string) ($counts[OrderStatus::Cancelled->value] ?? 0), ($counts[OrderStatus::Cancelled->value] ?? 0) > 0 ? 'down' : 'neutral', 'Not fulfilled', 'M18 6 6 18M6 6l12 12'),
            $this->pointMetric('Returned', (string) ($counts[OrderStatus::Returned->value] ?? 0), 'neutral', 'Sent back', 'M9 14 4 9l5-5M4 9h11a5 5 0 0 1 0 10h-1'),
            $this->pointMetric('Refunded', (string) ($counts[OrderStatus::Refunded->value] ?? 0), 'neutral', 'Money returned', 'M3 3v18h18'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function catalogStats(WidgetContext $context): array
    {
        $newProducts = Product::query()->whereBetween('created_at', [$context->start, $context->end])->count();

        return [
            $this->pointMetric('Active Products', (string) Product::query()->published()->count(), 'neutral', $newProducts.' new in range', 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z'),
            $this->pointMetric('Out of Stock', (string) Product::query()->published()->outOfStock()->count(), Product::query()->published()->outOfStock()->count() > 0 ? 'down' : 'neutral', 'Needs restock', 'M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z'),
            $this->pointMetric('Low Stock', (string) Product::query()->published()->lowStock()->count(), Product::query()->published()->lowStock()->count() > 0 ? 'down' : 'neutral', 'At/under threshold', 'M12 9v4m0 4h.01'),
            $this->pointMetric('Inventory Value', MoneyFormatter::formatCompact($this->inventoryValueCents()), 'neutral', 'Stock x price', 'M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7'),
            $this->pointMetric('Brands', (string) Brand::query()->count(), 'neutral', 'Total brands', 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z'),
            $this->pointMetric('Categories', (string) Category::query()->count(), 'neutral', 'Total categories', 'M4 4h16v4H4zM4 10h10v10H4zM16 10h4v10h-4z'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function customerStats(WidgetContext $context): array
    {
        [$prevStart, $prevEnd] = $this->previousBounds($context);

        $new = $this->newCustomers($context->start, $context->end);
        $prevNew = $this->newCustomers($prevStart, $prevEnd);

        $active = Customer::query()->active()->count();
        $subscribers = NewsletterSubscriber::query()->subscribed()->count();
        $repeat = $this->repeatCustomerCount($context->start, $context->end);

        return [
            $this->metric('New Customers', (string) $new, $new, $prevNew, 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'),
            $this->pointMetric('Active Customers', (string) $active, 'neutral', 'Currently active', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2'),
            $this->pointMetric('Repeat Buyers', (string) $repeat, 'neutral', '2+ completed orders', 'M17 1l4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3'),
            $this->pointMetric('Newsletter', (string) $subscribers, 'neutral', 'Subscribers', 'M4 4h16v16H4zM4 7l8 6 8-6'),
        ];
    }

    // ---------------------------------------------------------------------
    // Charts
    // ---------------------------------------------------------------------

    /**
     * @return array{type: string, labels: array<int, string>, series: array<int, array{name: string, data: array<int, float|int>}>}
     */
    public function salesTrend(WidgetContext $context): array
    {
        [$grouping, $buckets] = $this->buildBuckets($context);

        Order::query()
            ->whereIn('status', self::revenueStatuses())
            ->whereBetween('placed_at', [$context->start, $context->end])
            ->get(['placed_at', 'total_cents'])
            ->each(function (Order $order) use (&$buckets, $grouping): void {
                $key = $this->bucketKey(CarbonImmutable::parse($order->placed_at), $grouping);
                if (isset($buckets[$key])) {
                    $buckets[$key]['value'] += $order->total_cents / 100;
                }
            });

        return [
            'type' => 'area',
            'labels' => array_column($buckets, 'label'),
            'series' => [['name' => 'Revenue', 'data' => array_map(fn ($b) => round($b['value'], 2), array_values($buckets))]],
        ];
    }

    /**
     * @return array{type: string, labels: array<int, string>, series: array<int, array{name: string, data: array<int, float|int>}>}
     */
    public function ordersTrend(WidgetContext $context): array
    {
        [$grouping, $buckets] = $this->buildBuckets($context);

        Order::query()
            ->whereBetween('placed_at', [$context->start, $context->end])
            ->get(['placed_at'])
            ->each(function (Order $order) use (&$buckets, $grouping): void {
                $key = $this->bucketKey(CarbonImmutable::parse($order->placed_at), $grouping);
                if (isset($buckets[$key])) {
                    $buckets[$key]['value']++;
                }
            });

        return [
            'type' => 'bar',
            'labels' => array_column($buckets, 'label'),
            'series' => [['name' => 'Orders', 'data' => array_map(fn ($b) => (int) $b['value'], array_values($buckets))]],
        ];
    }

    /**
     * @return array{type: string, labels: array<int, string>, series: array<int, array{name: string, data: array<int, int>}>}
     */
    public function orderStatusBreakdown(WidgetContext $context): array
    {
        $counts = $this->ordersCountByStatus($context->start, $context->end);

        $labels = [];
        $data = [];

        foreach (OrderStatus::cases() as $status) {
            $count = (int) ($counts[$status->value] ?? 0);
            if ($count > 0) {
                $labels[] = $status->label();
                $data[] = $count;
            }
        }

        return [
            'type' => 'donut',
            'labels' => $labels,
            'series' => [['name' => 'Orders', 'data' => $data]],
        ];
    }

    /**
     * @return array{type: string, labels: array<int, string>, series: array<int, array{name: string, data: array<int, float>}>}
     */
    public function topCategoriesChart(WidgetContext $context): array
    {
        $rows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereIn('orders.status', collect(self::revenueStatuses())->map(fn (OrderStatus $s) => $s->value)->all())
            ->whereBetween('orders.placed_at', [$context->start, $context->end])
            ->groupBy('categories.id', 'categories.name')
            ->select('categories.name as label', DB::raw('SUM(order_items.line_total_cents) as revenue_cents'))
            ->orderByDesc('revenue_cents')
            ->limit(6)
            ->get();

        return [
            'type' => 'donut',
            'labels' => $rows->pluck('label')->all(),
            'series' => [['name' => 'Revenue', 'data' => $rows->map(fn ($r) => round($r->revenue_cents / 100, 2))->all()]],
        ];
    }

    /**
     * @return array{type: string, labels: array<int, string>, series: array<int, array{name: string, data: array<int, int>}>}
     */
    public function inventoryStatusChart(): array
    {
        $inStock = Product::query()->published()->inStock()->whereColumn('stock_quantity', '>', 'low_stock_threshold')->count();
        $lowStock = Product::query()->published()->lowStock()->count();
        $outOfStock = Product::query()->published()->outOfStock()->count();

        return [
            'type' => 'donut',
            'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
            'series' => [['name' => 'Products', 'data' => [$inStock, $lowStock, $outOfStock]]],
        ];
    }

    /**
     * @return array{type: string, labels: array<int, string>, series: array<int, array{name: string, data: array<int, int>}>}
     */
    public function customerGrowth(WidgetContext $context): array
    {
        [$grouping, $buckets] = $this->buildBuckets($context);

        Customer::query()
            ->whereBetween('created_at', [$context->start, $context->end])
            ->get(['created_at'])
            ->each(function (Customer $customer) use (&$buckets, $grouping): void {
                $key = $this->bucketKey(CarbonImmutable::parse($customer->created_at), $grouping);
                if (isset($buckets[$key])) {
                    $buckets[$key]['value']++;
                }
            });

        return [
            'type' => 'line',
            'labels' => array_column($buckets, 'label'),
            'series' => [['name' => 'New Customers', 'data' => array_map(fn ($b) => (int) $b['value'], array_values($buckets))]],
        ];
    }

    // ---------------------------------------------------------------------
    // Tables & lists
    // ---------------------------------------------------------------------

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function recentOrders(WidgetContext $context, int $limit = 8): Collection
    {
        return Order::query()
            ->with('customer:id,name')
            ->whereBetween('placed_at', [$context->start, $context->end])
            ->latest('placed_at')
            ->limit($limit)
            ->get()
            ->map(fn (Order $order): array => [
                'id' => $order->id,
                'number' => $order->order_number,
                'customer' => $order->customer?->name ?? 'Guest',
                'total' => MoneyFormatter::format($order->total_cents),
                'status' => $order->status->label(),
                'status_variant' => $order->status->badgeVariant(),
                'placed_at' => optional($order->placed_at)->diffForHumans(),
                'url' => route('admin.orders.show', $order),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function lowStock(int $limit = 8): Collection
    {
        return Product::query()
            ->published()
            ->lowStock()
            ->orderBy('stock_quantity')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'sku', 'stock_quantity', 'low_stock_threshold'])
            ->map(fn (Product $product): array => [
                'name' => $product->name,
                'sku' => $product->sku,
                'stock' => $product->stock_quantity,
                'threshold' => $product->low_stock_threshold,
                'url' => route('admin.inventory.show', $product),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function bestSellingProducts(WidgetContext $context, int $limit = 6): Collection
    {
        $rows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', collect(self::revenueStatuses())->map(fn (OrderStatus $s) => $s->value)->all())
            ->whereBetween('orders.placed_at', [$context->start, $context->end])
            ->groupBy('order_items.product_id')
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total_cents) as revenue_cents'),
            )
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->get();

        $products = Product::query()->whereIn('id', $rows->pluck('product_id'))->get(['id', 'name', 'sku'])->keyBy('id');

        return $rows->map(fn ($row): array => [
            'name' => $products[$row->product_id]->name ?? 'Unknown product',
            'sku' => $products[$row->product_id]->sku ?? '—',
            'units' => (int) $row->units_sold,
            'revenue' => MoneyFormatter::format((int) $row->revenue_cents),
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function latestCustomers(int $limit = 6): Collection
    {
        return Customer::query()
            ->latest()
            ->limit($limit)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(fn (Customer $customer): array => [
                'name' => $customer->name,
                'email' => $customer->email,
                'joined' => optional($customer->created_at)->diffForHumans(),
                'url' => route('admin.customers.show', $customer),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function topCustomers(WidgetContext $context, int $limit = 6): Collection
    {
        $rows = Order::query()
            ->whereIn('status', self::revenueStatuses())
            ->whereBetween('placed_at', [$context->start, $context->end])
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->select('customer_id', DB::raw('SUM(total_cents) as spent_cents'), DB::raw('COUNT(*) as orders_count'))
            ->orderByDesc('spent_cents')
            ->limit($limit)
            ->get();

        $customers = Customer::query()->whereIn('id', $rows->pluck('customer_id'))->get(['id', 'name', 'email'])->keyBy('id');

        return $rows->map(fn ($row): array => [
            'name' => $customers[$row->customer_id]->name ?? 'Unknown',
            'email' => $customers[$row->customer_id]->email ?? '—',
            'spent' => MoneyFormatter::format((int) $row->spent_cents),
            'orders' => (int) $row->orders_count,
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function latestReviews(int $limit = 6): Collection
    {
        return Review::query()
            ->with('product:id,name')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Review $review): array => [
                'product' => $review->product?->name ?? '—',
                'author' => $review->author_name,
                'rating' => (int) $review->rating,
                'title' => $review->title,
                'approved' => (bool) $review->is_approved,
                'created' => optional($review->created_at)->diffForHumans(),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function activityTimeline(WidgetContext $context, int $limit = 10): Collection
    {
        return AuditLog::query()
            ->with(['causer', 'subject'])
            ->whereBetween('created_at', [$context->start, $context->end])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (AuditLog $log): array => [
                'description' => $log->description,
                'category' => $log->category?->value ?? 'system',
                'causer' => $log->causerName(),
                'subject' => $log->subjectLabel(),
                'created' => optional($log->created_at)->diffForHumans(),
            ]);
    }

    /**
     * Geographic breakdown of completed orders. Future-ready: aggregates on
     * whatever region field exists today (state, then city) so a Bangladesh
     * district dataset can be swapped in without touching the widget.
     *
     * @return array<int, array<string, mixed>>
     */
    public function ordersByRegion(WidgetContext $context, int $limit = 8): array
    {
        $regions = [];

        Order::query()
            ->whereBetween('placed_at', [$context->start, $context->end])
            ->get(['shipping_address', 'total_cents', 'customer_id', 'status'])
            ->each(function (Order $order) use (&$regions): void {
                $address = $this->decodeAddress($order->shipping_address);
                $region = $address['state'] ?? $address['city'] ?? 'Unknown';

                $regions[$region] ??= ['region' => $region, 'orders' => 0, 'revenue_cents' => 0, 'customers' => []];
                $regions[$region]['orders']++;

                if (in_array($order->status, self::revenueStatuses(), true)) {
                    $regions[$region]['revenue_cents'] += (int) $order->total_cents;
                }

                if ($order->customer_id) {
                    $regions[$region]['customers'][$order->customer_id] = true;
                }
            });

        return collect($regions)
            ->map(fn (array $row): array => [
                'region' => $row['region'],
                'orders' => $row['orders'],
                'revenue' => MoneyFormatter::format($row['revenue_cents']),
                'revenue_cents' => $row['revenue_cents'],
                'customers' => count($row['customers']),
            ])
            ->sortByDesc('orders')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function marketingCalendar(WidgetContext $context): Collection
    {
        $events = collect();

        Offer::query()
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $context->start))
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $context->end))
            ->orderBy('starts_at')
            ->limit(10)
            ->get()
            ->each(fn (Offer $offer) => $events->push($this->calendarEvent('Offer', $offer->name, $offer->starts_at, $offer->ends_at, $offer->is_active)));

        Promotion::query()
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $context->start))
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $context->end))
            ->orderBy('starts_at')
            ->limit(10)
            ->get()
            ->each(fn (Promotion $promotion) => $events->push($this->calendarEvent('Promotion', $promotion->name, $promotion->starts_at, $promotion->ends_at, $promotion->is_active)));

        Discount::query()
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $context->start))
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $context->end))
            ->orderBy('starts_at')
            ->limit(10)
            ->get()
            ->each(fn (Discount $discount) => $events->push($this->calendarEvent('Discount', $discount->name ?? $discount->code, $discount->starts_at, $discount->ends_at, $discount->is_active)));

        return $events->sortBy('starts_sort')->values();
    }

    // ---------------------------------------------------------------------
    // Primitives
    // ---------------------------------------------------------------------

    public function completedRevenueCents(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return (int) Order::query()
            ->whereIn('status', self::revenueStatuses())
            ->whereBetween('placed_at', [$start, $end])
            ->sum('total_cents');
    }

    public function ordersPlaced(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return Order::query()->whereBetween('placed_at', [$start, $end])->count();
    }

    /**
     * @return array<string, int>
     */
    public function ordersCountByStatus(CarbonImmutable $start, CarbonImmutable $end): array
    {
        return Order::query()
            ->whereBetween('placed_at', [$start, $end])
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    public function averageOrderValueCents(CarbonImmutable $start, CarbonImmutable $end): int
    {
        $completed = Order::query()
            ->whereIn('status', self::revenueStatuses())
            ->whereBetween('placed_at', [$start, $end])
            ->count();

        if ($completed === 0) {
            return 0;
        }

        return (int) round($this->completedRevenueCents($start, $end) / $completed);
    }

    public function newCustomers(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return Customer::query()->whereBetween('created_at', [$start, $end])->count();
    }

    public function inventoryValueCents(): int
    {
        return (int) Product::query()->sum(DB::raw('stock_quantity * price_cents'));
    }

    public function repeatCustomerCount(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return Order::query()
            ->whereIn('status', self::revenueStatuses())
            ->whereBetween('placed_at', [$start, $end])
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) >= 2')
            ->get(['customer_id'])
            ->count();
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function previousBounds(WidgetContext $context): array
    {
        $length = $context->start->diffInSeconds($context->end) + 1;
        $prevEnd = $context->start->subSecond();
        $prevStart = $prevEnd->subSeconds($length - 1);

        return [$prevStart, $prevEnd];
    }

    /**
     * @return array{0: string, 1: array<string, array{label: string, value: float|int}>}
     */
    private function buildBuckets(WidgetContext $context): array
    {
        $grouping = $context->grouping();
        $buckets = [];

        $cursor = $grouping === 'month' ? $context->start->startOfMonth() : $context->start->startOfDay();

        while ($cursor->lessThanOrEqualTo($context->end)) {
            if ($grouping === 'month') {
                $buckets[$cursor->format('Y-m')] = ['label' => $cursor->format('M Y'), 'value' => 0];
                $cursor = $cursor->addMonthNoOverflow();
            } else {
                $buckets[$cursor->format('Y-m-d')] = ['label' => $cursor->format('M j'), 'value' => 0];
                $cursor = $cursor->addDay();
            }
        }

        return [$grouping, $buckets];
    }

    private function bucketKey(CarbonImmutable $date, string $grouping): string
    {
        return $grouping === 'month' ? $date->format('Y-m') : $date->format('Y-m-d');
    }

    /**
     * @return array<string, mixed>
     */
    private function metric(string $label, string $value, float|int $current, float|int $previous, string $icon): array
    {
        [$change, $trend] = $this->comparison($current, $previous);

        return ['label' => $label, 'value' => $value, 'change' => $change, 'trend' => $trend, 'icon' => $icon];
    }

    /**
     * @return array<string, mixed>
     */
    private function pointMetric(string $label, string $value, string $trend, string $change, string $icon): array
    {
        return ['label' => $label, 'value' => $value, 'change' => $change, 'trend' => $trend, 'icon' => $icon];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function comparison(float|int $current, float|int $previous): array
    {
        if ($previous <= 0) {
            return $current > 0 ? ['New activity', 'up'] : ['No prior data', 'neutral'];
        }

        $pct = round((($current - $previous) / $previous) * 100, 1);
        $trend = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'neutral');
        $sign = $pct > 0 ? '+' : '';

        return [$sign.$pct.'% vs previous', $trend];
    }

    /**
     * @param  mixed  $raw
     * @return array<string, mixed>
     */
    private function decodeAddress($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }

        if (is_string($raw) && $raw !== '') {
            return json_decode($raw, true) ?: [];
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function calendarEvent(string $type, ?string $title, $startsAt, $endsAt, bool $active): array
    {
        return [
            'type' => $type,
            'title' => $title ?? $type,
            'starts_at' => $startsAt ? CarbonImmutable::parse($startsAt)->format('M j, Y') : 'Always on',
            'ends_at' => $endsAt ? CarbonImmutable::parse($endsAt)->format('M j, Y') : null,
            'starts_sort' => $startsAt ? CarbonImmutable::parse($startsAt)->timestamp : 0,
            'active' => $active,
        ];
    }
}
