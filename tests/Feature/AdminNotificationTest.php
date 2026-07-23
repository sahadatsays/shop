<?php

use App\Enums\NotificationAudience;
use App\Enums\NotificationCategory;
use App\Enums\OrderStatus;
use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('admin notifications history page renders', function (): void {
    $admin = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();

    AppNotification::query()->create([
        'notifiable_type' => $admin->getMorphClass(),
        'notifiable_id' => $admin->id,
        'audience' => NotificationAudience::Admin,
        'category' => NotificationCategory::SystemAlert,
        'title' => 'Maintenance scheduled',
        'body' => 'Checkout may be unavailable Sunday at 2 AM.',
    ]);

    $this->get(route('admin.notifications.index'))
        ->assertSuccessful()
        ->assertSee('Notifications')
        ->assertSee('Maintenance scheduled');
});

test('admin can mark notification as read', function (): void {
    $admin = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();

    $notification = AppNotification::query()->create([
        'notifiable_type' => $admin->getMorphClass(),
        'notifiable_id' => $admin->id,
        'audience' => NotificationAudience::Admin,
        'category' => NotificationCategory::Inventory,
        'title' => 'Low stock alert',
        'body' => 'Heritage Wool Beanie — 4 left.',
    ]);

    $this->patch(route('admin.notifications.read', $notification))
        ->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('admin can mark all notifications as read', function (): void {
    $admin = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();

    AppNotification::query()->create([
        'notifiable_type' => $admin->getMorphClass(),
        'notifiable_id' => $admin->id,
        'audience' => NotificationAudience::Admin,
        'category' => NotificationCategory::SystemAlert,
        'title' => 'Alert one',
        'body' => 'First alert.',
    ]);

    AppNotification::query()->create([
        'notifiable_type' => $admin->getMorphClass(),
        'notifiable_id' => $admin->id,
        'audience' => NotificationAudience::Admin,
        'category' => NotificationCategory::OrderUpdate,
        'title' => 'Alert two',
        'body' => 'Second alert.',
    ]);

    $this->post(route('admin.notifications.mark-all-read'))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(AppNotification::query()->forNotifiable($admin)->unread()->count())->toBe(0);
});

test('order status updates create admin and customer notifications', function (): void {
    $order = Order::factory()->create([
        'status' => OrderStatus::Pending,
        'customer_id' => Customer::query()->active()->firstOrFail()->id,
    ]);

    $this->patch(route('admin.orders.status.update', $order), [
        'status' => OrderStatus::Shipped->value,
        'message' => 'Shipped via FedEx.',
    ])->assertRedirect(route('admin.orders.show', $order));

    $admin = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();

    expect(AppNotification::query()->forNotifiable($admin)->count())->toBeGreaterThan(0)
        ->and(AppNotification::query()->forNotifiable($order->customer)->count())->toBeGreaterThan(0)
        ->and(AppNotification::query()->forNotifiable($order->customer)->first()->title)
        ->toContain('on the way');
});

test('admin notifications require permission', function (): void {
    actingAsAdmin('inventory_manager');

    $this->get(route('admin.notifications.index'))->assertSuccessful();

    auth('admin')->logout();
    actingAsAdmin('product_manager');

    $this->get(route('admin.notifications.index'))->assertForbidden();
});
