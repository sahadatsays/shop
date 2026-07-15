<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTOs\Admin\Dashboard\ChartData;
use App\DTOs\Admin\Dashboard\DashboardViewData;
use App\DTOs\Admin\Dashboard\QuickActionData;
use App\DTOs\Admin\Dashboard\StatMetricData;
use App\Support\MoneyFormatter;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private ProductRepositoryInterface $products,
        private CustomerRepositoryInterface $customers,
        private CategoryRepositoryInterface $categories,
    ) {}

    public function getViewData(): DashboardViewData
    {
        $monthly = $this->orders->monthlyMetrics();
        $inventory = $this->products->inventoryStatusCounts();
        $topCategories = $this->categories->topByRevenue();

        return new DashboardViewData(
            stats: $this->buildStats(),
            charts: $this->buildCharts($monthly, $inventory, $topCategories),
            recentOrders: $this->orders->recent(),
            latestCustomers: $this->customers->latest(),
            topProducts: $this->products->topSelling(),
            lowStockProducts: $this->products->lowStock(),
            quickActions: $this->buildQuickActions(),
        );
    }

    /**
     * @return array<int, StatMetricData>
     */
    private function buildStats(): array
    {
        $revenueCents = $this->orders->sumRevenueCents();
        $todaySales = $this->orders->sumRevenueTodayCents();
        $todayOrders = $this->orders->countToday();

        return [
            new StatMetricData(
                label: "Today's Sales",
                value: MoneyFormatter::format($todaySales),
                change: $todayOrders.' orders today',
                trend: $todaySales > 0 ? 'up' : 'neutral',
                icon: 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6',
            ),
            new StatMetricData(
                label: "Today's Orders",
                value: (string) $todayOrders,
                change: 'Placed since midnight',
                trend: $todayOrders > 0 ? 'up' : 'neutral',
                icon: 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z',
            ),
            new StatMetricData(
                label: 'Revenue',
                value: MoneyFormatter::formatCompact($revenueCents),
                change: 'All-time total',
                trend: 'up',
                icon: 'M3 3v18h18M7 16l4-4 4 4 5-6',
            ),
            new StatMetricData(
                label: 'Customers',
                value: (string) $this->customers->countTotal(),
                change: 'Registered accounts',
                trend: 'neutral',
                icon: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm11 4a3 3 0 1 0 0-6M22 21v-2a4 4 0 0 0-3-3.87',
            ),
            new StatMetricData(
                label: 'Products',
                value: (string) $this->products->countActive(),
                change: 'Active catalog items',
                trend: 'neutral',
                icon: 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z',
            ),
            new StatMetricData(
                label: 'Pending Orders',
                value: (string) $this->orders->countPending(),
                change: 'Awaiting fulfillment',
                trend: $this->orders->countPending() > 0 ? 'up' : 'neutral',
                icon: 'M12 8v4l3 3M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z',
            ),
            new StatMetricData(
                label: 'Low Stock',
                value: (string) $this->products->countLowStock(),
                change: 'Items at or below threshold',
                trend: $this->products->countLowStock() > 0 ? 'down' : 'neutral',
                icon: 'M12 9v4M12 17h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z',
            ),
        ];
    }

    /**
     * @param  Collection<int, array{month: string, label: string, sales_cents: int, order_count: int}>  $monthly
     * @param  array{in_stock: int, low_stock: int, out_of_stock: int}  $inventory
     * @param  Collection<int, array{label: string, value: int}>  $topCategories
     * @return array<int, ChartData>
     */
    private function buildCharts($monthly, array $inventory, $topCategories): array
    {
        $labels = $monthly->pluck('label')->all();
        $salesDollars = $monthly->map(fn (array $row): float => round($row['sales_cents'] / 100, 2))->all();
        $orderCounts = $monthly->pluck('order_count')->all();
        $revenueDollars = $salesDollars;

        return [
            new ChartData(
                id: 'sales-by-month',
                title: 'Sales by Month',
                type: 'area',
                labels: $labels,
                series: [['name' => 'Sales', 'data' => $salesDollars]],
            ),
            new ChartData(
                id: 'orders-by-month',
                title: 'Orders by Month',
                type: 'bar',
                labels: $labels,
                series: [['name' => 'Orders', 'data' => $orderCounts]],
            ),
            new ChartData(
                id: 'revenue-by-month',
                title: 'Revenue by Month',
                type: 'line',
                labels: $labels,
                series: [['name' => 'Revenue', 'data' => $revenueDollars]],
            ),
            new ChartData(
                id: 'top-categories',
                title: 'Top Categories',
                type: 'donut',
                labels: $topCategories->pluck('label')->all(),
                series: [['name' => 'Revenue', 'data' => $topCategories->pluck('value')->all()]],
            ),
            new ChartData(
                id: 'inventory-status',
                title: 'Inventory Status',
                type: 'donut',
                labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                series: [[
                    'name' => 'Products',
                    'data' => [
                        $inventory['in_stock'],
                        $inventory['low_stock'],
                        $inventory['out_of_stock'],
                    ],
                ]],
            ),
        ];
    }

    /**
     * @return array<int, QuickActionData>
     */
    private function buildQuickActions(): array
    {
        return [
            new QuickActionData(
                label: 'Add Product',
                href: null,
                icon: 'M12 5v14M5 12h14',
                description: 'Create a new catalog item',
            ),
            new QuickActionData(
                label: 'Create Order',
                href: null,
                icon: 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z',
                description: 'Manually place an order',
            ),
            new QuickActionData(
                label: 'Manage Customers',
                href: null,
                icon: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
                description: 'View and edit customer records',
            ),
            new QuickActionData(
                label: 'View Reports',
                href: null,
                icon: 'M3 3v18h18M7 16l4-4 4 4 5-6',
                description: 'Open analytics and exports',
            ),
        ];
    }
}
