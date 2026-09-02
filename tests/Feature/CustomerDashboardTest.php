<?php

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
});

test('guest cannot access customer dashboard', function (): void {
    $this->get(route('account'))->assertRedirect(route('login'));
});

test('customer dashboard shows personalized welcome and order data', function (): void {
    $customer = Customer::query()->has('orders')->firstOrFail();
    $latestOrder = $customer->orders()->latest('placed_at')->firstOrFail();

    actingAsCustomer($customer);

    $this->get(route('account'))
        ->assertSuccessful()
        ->assertSee('Welcome back,', false)
        ->assertSee(explode(' ', trim($customer->name))[0], false)
        ->assertSee('Recent orders')
        ->assertSee('#'.$latestOrder->order_number)
        ->assertSee('Reward points')
        ->assertSee('Recommended for you');
});

test('customer dashboard stats reflect order history', function (): void {
    $customer = Customer::factory()->create();

    Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => OrderStatus::Shipped,
        'placed_at' => now()->subDays(2),
        'estimated_delivery_at' => now()->addDays(3),
    ]);

    actingAsCustomer($customer);

    $this->get(route('account'))
        ->assertSuccessful()
        ->assertSee('Total orders', false)
        ->assertSee('In transit', false)
        ->assertSee('tracking-heading', false);
});

test('customer dashboard shows empty state when customer has no orders', function (): void {
    $customer = Customer::factory()->create();

    actingAsCustomer($customer);

    $this->get(route('account'))
        ->assertSuccessful()
        ->assertSee('No orders yet')
        ->assertSee('No active shipments');
});
