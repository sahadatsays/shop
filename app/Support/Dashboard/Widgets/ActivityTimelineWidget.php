<?php

namespace App\Support\Dashboard\Widgets;

use App\Services\Admin\Dashboard\DashboardMetrics;
use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;

class ActivityTimelineWidget extends AbstractWidgetProvider
{
    public function __construct(private readonly DashboardMetrics $metrics) {}

    public function data(WidgetContext $context): array
    {
        return ['events' => $this->metrics->activityTimeline($context)];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.activity-timeline';
    }
}
