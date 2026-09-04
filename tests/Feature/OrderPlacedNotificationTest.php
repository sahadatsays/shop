<?php

use App\Enums\OrderStatus;
use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\NotificationService;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
});

test('placing an order notifies admins and customers with a valid email', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();
    $admin = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => 'order-notify@example.com',
        'shipping' => [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'line1' => '123 Main Street',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'delivery_method' => 'standard',
        'payment_method' => 'cod',
        'terms_accepted' => '1',
    ])->assertRedirect();

    $customer = Customer::query()->where('email', 'order-notify@example.com')->firstOrFail();
    $order = Order::query()->where('customer_id', $customer->id)->latest('id')->firstOrFail();

    expect(AppNotification::query()->forNotifiable($admin)->count())->toBeGreaterThan(0)
        ->and(AppNotification::query()->forNotifiable($admin)->latestFirst()->first()?->title)
        ->toContain($order->order_number)
        ->and(AppNotification::query()->forNotifiable($customer)->count())->toBe(1)
        ->and(AppNotification::query()->forNotifiable($customer)->first()?->title)
        ->toContain($order->order_number);
});

test('placing an order notifies admins but skips customer when email is not notifiable', function (): void {
    $customer = Customer::factory()->create([
        'email' => 'google-user-1@oauth.local',
    ]);

    actingAsCustomer($customer);

    $product = Product::query()->published()->inStock()->firstOrFail();
    $admin = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => 'google-user-1@oauth.local',
        'shipping' => [
            'first_name' => 'OAuth',
            'last_name' => 'User',
            'line1' => '123 Main Street',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'delivery_method' => 'standard',
        'payment_method' => 'cod',
        'terms_accepted' => '1',
    ])->assertRedirect();

    expect(AppNotification::query()->forNotifiable($admin)->count())->toBeGreaterThan(0)
        ->and(AppNotification::query()->forNotifiable($customer)->count())->toBe(0);
});

test('notify order placed always notifies admins and only notifies customers with valid email', function (): void {
    $admin = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();
    $notifications = app(NotificationService::class);

    $validCustomer = Customer::factory()->create(['email' => 'valid@example.com']);
    $invalidCustomer = Customer::factory()->create(['email' => 'invalid-email']);

    $validOrder = Order::factory()->create([
        'customer_id' => $validCustomer->id,
        'status' => OrderStatus::Pending,
    ]);

    $invalidOrder = Order::factory()->create([
        'customer_id' => $invalidCustomer->id,
        'status' => OrderStatus::Pending,
    ]);

    AppNotification::query()->delete();

    $notifications->notifyOrderPlaced($validOrder);
    $notifications->notifyOrderPlaced($invalidOrder);

    expect(AppNotification::query()->forNotifiable($admin)->count())->toBe(2)
        ->and(AppNotification::query()->forNotifiable($validCustomer)->count())->toBe(1)
        ->and(AppNotification::query()->forNotifiable($invalidCustomer)->count())->toBe(0);
});

test('customer hasNotifiableEmail rejects oauth placeholder and invalid addresses', function (): void {
    $oauthCustomer = Customer::factory()->make(['email' => 'google-user-1@oauth.local']);
    $invalidCustomer = Customer::factory()->make(['email' => 'not-an-email']);
    $validCustomer = Customer::factory()->make(['email' => 'shopper@example.com']);

    expect($oauthCustomer->hasNotifiableEmail())->toBeFalse()
        ->and($invalidCustomer->hasNotifiableEmail())->toBeFalse()
        ->and($validCustomer->hasNotifiableEmail())->toBeTrue();
});
