<?php

namespace App\Support\Dashboard\Widgets;

use App\Models\AppNotification;
use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;
use Illuminate\Support\Collection;

class NotificationsWidget extends AbstractWidgetProvider
{
    public function data(WidgetContext $context): array
    {
        if ($context->user === null) {
            return ['notifications' => new Collection, 'unread' => 0];
        }

        $notifications = AppNotification::query()
            ->forNotifiable($context->user)
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (AppNotification $notification): array => [
                'id' => $notification->id,
                'title' => $notification->title,
                'body' => $notification->body,
                'category' => $notification->category?->value ?? 'system',
                'time' => $notification->timeAgo(),
                'read' => $notification->isRead(),
                'action_url' => $notification->action_url,
                'action_label' => $notification->action_label,
            ]);

        return [
            'notifications' => $notifications,
            'unread' => AppNotification::query()->forNotifiable($context->user)->unread()->count(),
        ];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.notifications';
    }

    public function cacheTtl(): int
    {
        return 0;
    }
}
