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
        $customerId = session('customer_id');

        if (! $customerId) {
            $view->with('customerUnreadNotificationCount', 0);

            return;
        }

        $customer = Customer::query()->find($customerId);

        $view->with(
            'customerUnreadNotificationCount',
            $customer ? $this->notifications->unreadCountFor($customer) : 0,
        );
    }
}
