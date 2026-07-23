<?php

namespace App\DTOs\Admin\Dashboard;

use App\Enums\Admin\DashboardWidgetType;
use App\Models\DashboardWidget;

/**
 * A widget definition merged with the current user's saved preferences and
 * ready to render on the dashboard grid.
 */
final readonly class ResolvedWidget
{
    public function __construct(
        public DashboardWidget $widget,
        public bool $visible,
        public bool $collapsed,
        public bool $pinned,
        public int $position,
        public int $width,
        public bool $hasProvider,
    ) {}

    public function key(): string
    {
        return $this->widget->key;
    }

    public function name(): string
    {
        return $this->widget->name;
    }

    public function type(): DashboardWidgetType
    {
        return $this->widget->type;
    }

    public function isLazy(): bool
    {
        return $this->widget->type->isHeavy();
    }

    public function refreshInterval(): ?int
    {
        return $this->widget->refresh_interval;
    }
}
