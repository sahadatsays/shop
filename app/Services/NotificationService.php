<?php

namespace App\Services;

use App\Enums\NotificationAudience;
use App\Enums\NotificationCategory;
use App\Enums\OrderStatus;
use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * @param  array{
     *     audience: NotificationAudience,
     *     category: NotificationCategory,
     *     title: string,
     *     body: string,
     *     action_label?: string|null,
     *     action_url?: string|null,
     *     meta?: array<string, mixed>|null
     * }  $payload
     */
    public function notify(Model $notifiable, array $payload): AppNotification
    {
        return AppNotification::query()->create([
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'audience' => $payload['audience'],
            'category' => $payload['category'],
            'title' => $payload['title'],
            'body' => $payload['body'],
            'action_label' => $payload['action_label'] ?? null,
            'action_url' => $payload['action_url'] ?? null,
            'meta' => $payload['meta'] ?? null,
        ]);
    }

    /**
     * @param  array{
     *     category: NotificationCategory,
     *     title: string,
     *     body: string,
     *     action_label?: string|null,
     *     action_url?: string|null,
     *     meta?: array<string, mixed>|null
     * }  $payload
     */
    public function notifyAdminsWithPermission(string $permission, array $payload): void
    {
        User::query()
            ->where('is_active', true)
            ->with('roles.permissions')
            ->get()
            ->filter(fn (User $user): bool => $user->hasPermission($permission))
            ->each(function (User $user) use ($payload): void {
                $this->notify($user, [
                    ...$payload,
                    'audience' => NotificationAudience::Admin,
                ]);
            });
    }

    public function notifyOrderStatusChange(Order $order, OrderStatus $previousStatus, OrderStatus $status, ?string $message = null): void
    {
        $order->loadMissing('customer');

        $statusLabel = $status->label();
        $detail = $message ?: "Order status changed to {$statusLabel}.";

        $this->notifyAdminsWithPermission('orders.view', [
            'category' => NotificationCategory::OrderUpdate,
            'title' => "Order {$order->order_number} updated",
            'body' => $detail,
            'action_label' => 'View order',
            'action_url' => route('admin.orders.show', $order),
            'meta' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'previous_status' => $previousStatus->value,
                'status' => $status->value,
            ],
        ]);

        if (! $order->customer) {
            return;
        }

        [$title, $body, $actionLabel, $actionUrl] = $this->customerOrderStatusCopy($order, $status, $message);

        $this->notify($order->customer, [
            'audience' => NotificationAudience::Customer,
            'category' => NotificationCategory::OrderUpdate,
            'title' => $title,
            'body' => $body,
            'action_label' => $actionLabel,
            'action_url' => $actionUrl,
            'meta' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status->value,
            ],
        ]);
    }

    /**
     * @return array{0: string, 1: string, 2: string|null, 3: string|null}
     */
    private function customerOrderStatusCopy(Order $order, OrderStatus $status, ?string $message): array
    {
        return match ($status) {
            OrderStatus::Confirmed => [
                "Order {$order->order_number} confirmed",
                $message ?: 'We received your order and will begin processing it shortly.',
                'View order',
                route('account.orders'),
            ],
            OrderStatus::Processing, OrderStatus::Packed => [
                "Order {$order->order_number} is being prepared",
                $message ?: 'Your items are being prepared for shipment.',
                'View order',
                route('account.orders'),
            ],
            OrderStatus::Shipped => [
                'Your order is on the way',
                $message ?: "Order {$order->order_number} has shipped.",
                'Track shipment',
                route('track-order.create'),
            ],
            OrderStatus::Delivered => [
                "Order {$order->order_number} delivered",
                $message ?: 'Your order was delivered successfully.',
                'View order',
                route('account.orders'),
            ],
            OrderStatus::Cancelled => [
                "Order {$order->order_number} cancelled",
                $message ?: 'Your order was cancelled. Contact support if you need help.',
                'View order',
                route('account.orders'),
            ],
            OrderStatus::Returned => [
                "Return started for {$order->order_number}",
                $message ?: 'We are processing your return request.',
                'View order',
                route('account.orders'),
            ],
            OrderStatus::Refunded => [
                "Refund ready for {$order->order_number}",
                $message ?: 'Your refund has been approved and will be issued soon.',
                'View order',
                route('account.orders'),
            ],
            default => [
                "Order {$order->order_number} updated",
                $message ?: "Your order status is now {$status->label()}.",
                'View order',
                route('account.orders'),
            ],
        };
    }

    public function notifySystemAlert(User $user, string $title, string $body, ?string $actionUrl = null, ?string $actionLabel = null): AppNotification
    {
        return $this->notify($user, [
            'audience' => NotificationAudience::Admin,
            'category' => NotificationCategory::SystemAlert,
            'title' => $title,
            'body' => $body,
            'action_label' => $actionLabel,
            'action_url' => $actionUrl,
        ]);
    }

    /**
     * @param  array{category?: string|null, unread?: bool|null}  $filters
     */
    public function paginateFor(Model $notifiable, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AppNotification::query()->forNotifiable($notifiable);

        if (($filters['unread'] ?? null) === true) {
            $query->unread();
        }

        if ($category = $filters['category'] ?? null) {
            $query->where('category', $category);
        }

        return $query->latestFirst()->paginate($perPage)->withQueryString();
    }

    /**
     * @return Collection<int, AppNotification>
     */
    public function recentFor(Model $notifiable, int $limit = 8): Collection
    {
        return AppNotification::query()
            ->forNotifiable($notifiable)
            ->latestFirst()
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<string, Collection<int, AppNotification>>
     */
    public function groupedFor(Model $notifiable, ?string $category = null): Collection
    {
        $query = AppNotification::query()->forNotifiable($notifiable);

        if ($category) {
            $query->where('category', $category);
        }

        return $query
            ->latestFirst()
            ->get()
            ->groupBy(fn (AppNotification $notification): string => $notification->groupLabel());
    }

    public function unreadCountFor(Model $notifiable): int
    {
        return AppNotification::query()
            ->forNotifiable($notifiable)
            ->unread()
            ->count();
    }

    public function markAsRead(AppNotification $notification, Model $notifiable): AppNotification
    {
        abort_unless(
            $notification->notifiable_type === $notifiable->getMorphClass()
            && (int) $notification->notifiable_id === (int) $notifiable->getKey(),
            403,
        );

        $notification->markAsRead();

        return $notification->fresh();
    }

    public function markAllAsRead(Model $notifiable): int
    {
        return AppNotification::query()
            ->forNotifiable($notifiable)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function createSystemAlertsForActiveAdmins(string $title, string $body): void
    {
        User::query()
            ->where('is_active', true)
            ->each(fn (User $user) => $this->notifySystemAlert($user, $title, $body));
    }

    public function seedCustomerPromotion(Customer $customer, string $title, string $body, string $actionUrl, string $actionLabel = 'Shop now'): AppNotification
    {
        return $this->notify($customer, [
            'audience' => NotificationAudience::Customer,
            'category' => NotificationCategory::Promotion,
            'title' => $title,
            'body' => $body,
            'action_label' => $actionLabel,
            'action_url' => $actionUrl,
        ]);
    }

    public function seedInventoryAlert(User $user, string $title, string $body, ?string $actionUrl = null): AppNotification
    {
        return $this->notify($user, [
            'audience' => NotificationAudience::Admin,
            'category' => NotificationCategory::Inventory,
            'title' => $title,
            'body' => $body,
            'action_label' => $actionUrl ? 'View inventory' : null,
            'action_url' => $actionUrl,
        ]);
    }

    public function deleteForNotifiable(Model $notifiable): void
    {
        DB::transaction(function () use ($notifiable): void {
            AppNotification::query()
                ->forNotifiable($notifiable)
                ->delete();
        });
    }
}
