<?php

namespace App\Support\Dashboard\Widgets;

use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;

/**
 * Optional weather widget for delivery planning. Disabled by default. Wire a
 * real weather API here later; the widget already renders gracefully and is
 * toggleable from the Widget Management module.
 */
class WeatherWidget extends AbstractWidgetProvider
{
    public function data(WidgetContext $context): array
    {
        return [
            'location' => config('dashboard.weather.location', 'Set a location'),
            'configured' => (bool) config('dashboard.weather.api_key'),
        ];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.weather';
    }

    public function cacheTtl(): int
    {
        return 0;
    }
}
