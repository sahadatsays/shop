<?php

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminAccessSeeder;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function actingAsAdmin(?string $roleSlug = 'owner'): User
{
    $user = User::query()
        ->whereHas('roles', fn ($query) => $query->where('slug', $roleSlug))
        ->firstOrFail();

    test()->actingAs($user, 'admin');

    return $user;
}

function seedAdminAccess(): void
{
    test()->seed(AdminAccessSeeder::class);
}

function actingAsCustomer(?Customer $customer = null): Customer
{
    $customer ??= Customer::factory()->create();

    test()->actingAs($customer, 'customer');
    test()->withSession(['customer_id' => $customer->id]);

    return $customer;
}

function createDeliveredOrderForCustomer(Customer $customer, Product $product): Order
{
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'placed_at' => now()->subWeek(),
        'subtotal_cents' => $product->price_cents,
        'total_cents' => $product->price_cents,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price_cents' => $product->price_cents,
        'line_total_cents' => $product->price_cents,
    ]);

    return $order;
}
