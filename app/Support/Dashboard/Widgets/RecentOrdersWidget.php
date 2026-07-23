<?php

namespace App\Support\Dashboard\Widgets;

use App\Services\Admin\Dashboard\DashboardMetrics;
use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;

class RecentOrdersWidget extends AbstractWidgetProvider
{
    public function __construct(private readonly DashboardMetrics $metrics) {}

    public function data(WidgetContext $context): array
    {
        return ['orders' => $this->metrics->recentOrders($context)];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.recent-orders';
    }
}
