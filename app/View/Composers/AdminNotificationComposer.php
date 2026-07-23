<?php

namespace App\View\Composers;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminNotificationComposer
{
    public function __construct(private NotificationService $notifications) {}

    public function compose(View $view): void
    {
        $user = Auth::guard('admin')->user();

        if (! $user?->hasPermission('notifications.view')) {
            $view->with([
                'notifications' => collect(),
                'unreadCount' => 0,
            ]);

            return;
        }

        $view->with([
            'notifications' => $this->notifications->recentFor($user),
            'unreadCount' => $this->notifications->unreadCountFor($user),
        ]);
    }
}
