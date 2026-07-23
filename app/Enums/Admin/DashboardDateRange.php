<?php

namespace App\Enums\Admin;

use Carbon\CarbonImmutable;

enum DashboardDateRange: string
{
    case Today = 'today';
    case Yesterday = 'yesterday';
    case Last7Days = 'last_7_days';
    case Last30Days = 'last_30_days';
    case ThisMonth = 'this_month';
    case LastMonth = 'last_month';
    case ThisYear = 'this_year';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Today => 'Today',
            self::Yesterday => 'Yesterday',
            self::Last7Days => 'Last 7 Days',
            self::Last30Days => 'Last 30 Days',
            self::ThisMonth => 'This Month',
            self::LastMonth => 'Last Month',
            self::ThisYear => 'This Year',
            self::Custom => 'Custom Range',
        };
    }

    public static function fromKey(?string $key): self
    {
        return self::tryFrom((string) $key) ?? self::Last30Days;
    }

    /**
     * Resolve the concrete [start, end] bounds for this preset.
     *
     * @return array{start: CarbonImmutable, end: CarbonImmutable}
     */
    public function bounds(?string $from = null, ?string $to = null): array
    {
        $now = CarbonImmutable::now();

        return match ($this) {
            self::Today => [
                'start' => $now->startOfDay(),
                'end' => $now->endOfDay(),
            ],
            self::Yesterday => [
                'start' => $now->subDay()->startOfDay(),
                'end' => $now->subDay()->endOfDay(),
            ],
            self::Last7Days => [
                'start' => $now->subDays(6)->startOfDay(),
                'end' => $now->endOfDay(),
            ],
            self::Last30Days => [
                'start' => $now->subDays(29)->startOfDay(),
                'end' => $now->endOfDay(),
            ],
            self::ThisMonth => [
                'start' => $now->startOfMonth(),
                'end' => $now->endOfMonth(),
            ],
            self::LastMonth => [
                'start' => $now->subMonthNoOverflow()->startOfMonth(),
                'end' => $now->subMonthNoOverflow()->endOfMonth(),
            ],
            self::ThisYear => [
                'start' => $now->startOfYear(),
                'end' => $now->endOfYear(),
            ],
            self::Custom => $this->customBounds($from, $to, $now),
        };
    }

    /**
     * @return array{start: CarbonImmutable, end: CarbonImmutable}
     */
    private function customBounds(?string $from, ?string $to, CarbonImmutable $now): array
    {
        try {
            $start = $from !== null && $from !== '' ? CarbonImmutable::parse($from)->startOfDay() : $now->subDays(29)->startOfDay();
        } catch (\Throwable) {
            $start = $now->subDays(29)->startOfDay();
        }

        try {
            $end = $to !== null && $to !== '' ? CarbonImmutable::parse($to)->endOfDay() : $now->endOfDay();
        } catch (\Throwable) {
            $end = $now->endOfDay();
        }

        if ($end->lessThan($start)) {
            [$start, $end] = [$end->startOfDay(), $start->endOfDay()];
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case): array => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}
