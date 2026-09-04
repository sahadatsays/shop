<?php

namespace App\View\Composers;

use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\View\View;

class CustomerNotificationComposer
{
    public function __construct(private NotificationService $notifications) {}

    public function compose(View $view): void
    {
        /** @var Customer|null $customer */
        $customer = auth('customer')->user();

        if (! $customer) {
            $view->with('customerUnreadNotificationCount', 0);

            return;
        }

        $view->with(
            'customerUnreadNotificationCount',
            $this->notifications->unreadCountFor($customer),
        );
    }
}
