<?php

namespace App\Support\Dashboard\Widgets;

use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;

/**
 * Announcements are sourced from the widget's stored config so admins can edit
 * them through the Widget Management module without deploying code.
 */
class AnnouncementsWidget extends AbstractWidgetProvider
{
    public function data(WidgetContext $context): array
    {
        $configured = config('dashboard.announcements', []);

        $announcements = ! empty($configured) ? $configured : [
            [
                'title' => 'Welcome to the new dashboard',
                'body' => 'Widgets are now fully configurable — reorder, collapse, pin, or hide any card and your layout is saved automatically.',
                'type' => 'Release Notes',
                'date' => now()->format('M j, Y'),
            ],
        ];

        return ['announcements' => $announcements];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.announcements';
    }

    public function cacheTtl(): int
    {
        return 0;
    }
}
