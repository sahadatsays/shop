<?php

namespace App\Services\Notifications;

use App\Contracts\Notifications\OrderNotificationChannelInterface;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Collection;

class OrderNotificationDispatcher
{
    /**
     * @param  Collection<int, OrderNotificationChannelInterface>  $channels
     */
    public function __construct(private Collection $channels) {}

    public function dispatchStatusChange(
        Order $order,
        OrderStatus $previousStatus,
        OrderStatus $status,
        ?string $message = null,
    ): void {
        foreach ($this->channels as $channel) {
            $channel->notifyStatusChange($order, $previousStatus, $status, $message);
        }
    }
}
