<?php

namespace App\Enums\Admin;

enum DashboardWidgetType: string
{
    case StatGroup = 'stat_group';
    case Chart = 'chart';
    case Table = 'table';
    case Timeline = 'timeline';
    case Notifications = 'notifications';
    case QuickActions = 'quick_actions';
    case Map = 'map';
    case Calendar = 'calendar';
    case Announcement = 'announcement';
    case Weather = 'weather';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::StatGroup => 'Statistic Cards',
            self::Chart => 'Chart',
            self::Table => 'Table',
            self::Timeline => 'Activity Timeline',
            self::Notifications => 'Notifications',
            self::QuickActions => 'Quick Actions',
            self::Map => 'Business Map',
            self::Calendar => 'Calendar',
            self::Announcement => 'Announcement',
            self::Weather => 'Weather',
            self::Custom => 'Custom',
        };
    }

    /**
     * Widget bodies that are expensive and should be lazy-loaded / async.
     */
    public function isHeavy(): bool
    {
        return in_array($this, [self::Chart, self::Table, self::Timeline, self::Map, self::Calendar], true);
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
