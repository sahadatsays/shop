<?php

namespace App\DTOs\Admin\Dashboard;

use Illuminate\Support\Collection;

readonly class QuickActionData
{
    public function __construct(
        public string $label,
        public ?string $href,
        public string $icon,
        public string $description,
    ) {}
}

readonly class DashboardViewData
{
    /**
     * @param  array<int, StatMetricData>  $stats
     * @param  array<int, ChartData>  $charts
     * @param  Collection<int, RecentOrderData>  $recentOrders
     * @param  Collection<int, CustomerSummaryData>  $latestCustomers
     * @param  Collection<int, TopProductData>  $topProducts
     * @param  Collection<int, LowStockProductData>  $lowStockProducts
     * @param  Collection<int, FeaturedBrandData>  $featuredBrands
     * @param  array<int, QuickActionData>  $quickActions
     */
    public function __construct(
        public array $stats,
        public array $charts,
        public Collection $recentOrders,
        public Collection $latestCustomers,
        public Collection $topProducts,
        public Collection $lowStockProducts,
        public Collection $featuredBrands,
        public array $quickActions,
    ) {}
}
