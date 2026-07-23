<?php

namespace App\Support\Dashboard;

use App\Support\Dashboard\Contracts\WidgetProvider;

abstract class AbstractWidgetProvider implements WidgetProvider
{
    public function cacheTtl(): int
    {
        return (int) config('dashboard.cache_ttl', 300);
    }
}
