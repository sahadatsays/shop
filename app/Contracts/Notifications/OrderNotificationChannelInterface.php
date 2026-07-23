<?php

namespace App\Contracts\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;

interface OrderNotificationChannelInterface
{
    public function supports(string $channel): bool;

    public function notifyStatusChange(
        Order $order,
        OrderStatus $previousStatus,
        OrderStatus $status,
        ?string $message = null,
    ): void;
}
