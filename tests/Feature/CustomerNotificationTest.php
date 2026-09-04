<?php

use App\Enums\NotificationAudience;
use App\Enums\NotificationCategory;
use App\Models\AppNotification;
use App\Models\Customer;
use App\Services\NotificationService;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('customer notifications page requires login', function (): void {
    $this->get(route('account.notifications'))
        ->assertRedirect(route('login'));
});

test('customer can view notification history', function (): void {
    $customer = actingAsCustomer();

    AppNotification::query()->create([
        'notifiable_type' => $customer->getMorphClass(),
        'notifiable_id' => $customer->id,
        'audience' => NotificationAudience::Customer,
        'category' => NotificationCategory::OrderUpdate,
        'title' => 'Your order is on the way',
        'body' => 'Order VS-10001 has shipped.',
        'action_label' => 'Track shipment',
        'action_url' => route('track'),
    ]);

    $this->get(route('account.notifications'))
        ->assertSuccessful()
        ->assertSee('Your order is on the way')
        ->assertSee('Track shipment');
});

test('customer notifications page shows newest notifications first', function (): void {
    $customer = actingAsCustomer();

    AppNotification::query()->create([
        'notifiable_type' => $customer->getMorphClass(),
        'notifiable_id' => $customer->id,
        'audience' => NotificationAudience::Customer,
        'category' => NotificationCategory::OrderUpdate,
        'title' => 'Older order update',
        'body' => 'Shipped yesterday.',
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    AppNotification::query()->create([
        'notifiable_type' => $customer->getMorphClass(),
        'notifiable_id' => $customer->id,
        'audience' => NotificationAudience::Customer,
        'category' => NotificationCategory::Promotion,
        'title' => 'Latest promotion',
        'body' => 'Just for you.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $titles = app(NotificationService::class)
        ->groupedFor($customer)
        ->flatten(1)
        ->pluck('title')
        ->all();

    expect($titles)->toBe(['Latest promotion', 'Older order update']);
});

test('customer can mark notification as read', function (): void {
    $customer = actingAsCustomer();

    $notification = AppNotification::query()->create([
        'notifiable_type' => $customer->getMorphClass(),
        'notifiable_id' => $customer->id,
        'audience' => NotificationAudience::Customer,
        'category' => NotificationCategory::Promotion,
        'title' => 'Summer sale',
        'body' => '15% off field gear this week.',
    ]);

    $this->patchJson(route('account.notifications.read', $notification))
        ->assertOk()
        ->assertJsonPath('unread_count', 0);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('customer can mark all notifications as read', function (): void {
    $customer = actingAsCustomer();

    AppNotification::query()->create([
        'notifiable_type' => $customer->getMorphClass(),
        'notifiable_id' => $customer->id,
        'audience' => NotificationAudience::Customer,
        'category' => NotificationCategory::Account,
        'title' => 'Password updated',
        'body' => 'Your password was changed successfully.',
    ]);

    AppNotification::query()->create([
        'notifiable_type' => $customer->getMorphClass(),
        'notifiable_id' => $customer->id,
        'audience' => NotificationAudience::Customer,
        'category' => NotificationCategory::Promotion,
        'title' => 'New arrivals',
        'body' => 'Fresh gear just landed.',
    ]);

    $this->postJson(route('account.notifications.mark-all-read'))
        ->assertOk()
        ->assertJsonPath('unread_count', 0);

    expect(AppNotification::query()->forNotifiable($customer)->unread()->count())->toBe(0);
});

test('customer cannot mark another customers notification', function (): void {
    $customer = actingAsCustomer();
    $otherCustomer = Customer::query()->active()->whereKeyNot($customer->id)->firstOrFail();

    $notification = AppNotification::query()->create([
        'notifiable_type' => $otherCustomer->getMorphClass(),
        'notifiable_id' => $otherCustomer->id,
        'audience' => NotificationAudience::Customer,
        'category' => NotificationCategory::Account,
        'title' => 'Private alert',
        'body' => 'Should not be accessible.',
    ]);

    $this->patchJson(route('account.notifications.read', $notification))
        ->assertForbidden();
});
