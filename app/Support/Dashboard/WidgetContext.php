<?php

namespace App\Support\Dashboard;

use App\Enums\Admin\DashboardDateRange;
use App\Models\User;
use Carbon\CarbonImmutable;

/**
 * Immutable request context handed to every widget provider. Carries the
 * authenticated admin and the resolved global date-range bounds so widgets
 * compute figures for exactly the window the user selected.
 */
final class WidgetContext
{
    public function __construct(
        public readonly ?User $user,
        public readonly DashboardDateRange $range,
        public readonly CarbonImmutable $start,
        public readonly CarbonImmutable $end,
    ) {}

    public static function make(?User $user, DashboardDateRange $range, ?string $from = null, ?string $to = null): self
    {
        $bounds = $range->bounds($from, $to);

        return new self($user, $range, $bounds['start'], $bounds['end']);
    }

    /**
     * Grouping granularity for trend charts: daily for short ranges, monthly
     * for anything spanning more than two months.
     */
    public function grouping(): string
    {
        return $this->start->diffInDays($this->end) > 62 ? 'month' : 'day';
    }

    /**
     * A stable signature for this range, used to key cached widget payloads.
     */
    public function signature(): string
    {
        return $this->range->value.':'.$this->start->format('Ymd').':'.$this->end->format('Ymd');
    }
}
