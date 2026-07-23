<?php

namespace App\Support\Dashboard\Widgets;

use App\Services\Admin\Dashboard\DashboardMetrics;
use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;

class BestSellingProductsWidget extends AbstractWidgetProvider
{
    public function __construct(private readonly DashboardMetrics $metrics) {}

    public function data(WidgetContext $context): array
    {
        return ['products' => $this->metrics->bestSellingProducts($context)];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.best-selling-products';
    }
}
