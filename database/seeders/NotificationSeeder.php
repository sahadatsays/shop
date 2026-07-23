<?php

namespace Database\Seeders;

use App\Enums\NotificationCategory;
use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = app(NotificationService::class);

        $owner = User::query()->where('email', 'owner@valorsupply.co')->first();
        $inventoryManager = User::query()->where('email', 'inventory@valorsupply.co')->first();
        $customer = Customer::query()->active()->first();

        if ($owner) {
            $notifications->notifySystemAlert(
                $owner,
                'Store maintenance window scheduled',
                'Planned maintenance is scheduled for Sunday at 2:00 AM. Checkout may be unavailable for up to 30 minutes.',
            );
        }

        if ($inventoryManager) {
            $notifications->seedInventoryAlert(
                $inventoryManager,
                'Low stock alert',
                'Heritage Wool Beanie — 4 units remaining.',
                route('admin.inventory.index'),
            );
        }

        $order = Order::query()->with('customer')->latest('placed_at')->first();

        if ($order && $owner) {
            $notifications->notifyAdminsWithPermission('orders.view', [
                'category' => NotificationCategory::OrderUpdate,
                'title' => "New order {$order->order_number}",
                'body' => 'A new order was placed and is awaiting review.',
                'action_label' => 'View order',
                'action_url' => route('admin.orders.show', $order),
                'meta' => ['order_id' => $order->id],
            ]);
        }

        if ($customer) {
            $notifications->seedCustomerPromotion(
                $customer,
                'Summer field collection — 15% off',
                'Limited-time savings on jackets, packs, and outdoor essentials.',
                route('shop'),
            );

            AppNotification::query()
                ->forNotifiable($customer)
                ->latest()
                ->first()
                ?->markAsRead();
        }
    }
}
