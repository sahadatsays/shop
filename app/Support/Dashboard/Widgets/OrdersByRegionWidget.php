<?php

namespace App\Support\Dashboard\Widgets;

use App\Services\Admin\Dashboard\DashboardMetrics;
use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;

/**
 * Business map widget. Today it aggregates orders by the region field present
 * on shipping addresses (state, falling back to city). It is future-ready for
 * an interactive Bangladesh district map: swap the aggregation key and feed the
 * same {region, orders, revenue, customers} shape into a map renderer without
 * changing the engine, controller, or preferences.
 */
class OrdersByRegionWidget extends AbstractWidgetProvider
{
    public function __construct(private readonly DashboardMetrics $metrics) {}

    public function data(WidgetContext $context): array
    {
        return ['regions' => $this->metrics->ordersByRegion($context)];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.orders-by-region';
    }
}
