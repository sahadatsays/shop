<?php

use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Http\Resources\OrderTrackingResource;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
});

test('guest checkout with existing customer email is rejected', function (): void {
    Customer::factory()->create(['email' => 'existing@example.com']);

    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->from(route('checkout'))
        ->post(route('checkout.store'), [
            'email' => 'existing@example.com',
            'shipping' => [
                'first_name' => 'Alex',
                'last_name' => 'Guest',
                'line1' => '123 Main Street',
                'city' => 'Columbus',
                'state' => 'OH',
                'postal_code' => '43215',
                'country' => 'United States',
            ],
            'billing_same_as_shipping' => '1',
            'delivery_method' => 'standard',
            'payment_method' => 'card',
            'terms_accepted' => '1',
        ])
        ->assertRedirect(route('checkout'))
        ->assertSessionHasErrors('email');

    expect(Order::query()->whereHas('customer', fn ($q) => $q->where('email', 'existing@example.com'))->count())->toBe(0);
    expect(session('customer_id'))->toBeNull();
});

test('guest checkout does not set customer session after order', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => 'new-guest@example.com',
        'shipping' => [
            'first_name' => 'New',
            'last_name' => 'Guest',
            'line1' => '123 Main Street',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'billing_same_as_shipping' => '1',
        'delivery_method' => 'standard',
        'payment_method' => 'card',
        'terms_accepted' => '1',
    ])->assertRedirect();

    expect(session('customer_id'))->toBeNull();
    $this->get(route('account'))->assertRedirect(route('login'));
});

test('inventory manager cannot create products', function (): void {
    actingAsAdmin('inventory_manager');

    $category = Category::query()->firstOrFail();

    $this->post(route('admin.products.store'), [
        'name' => 'Unauthorized Product',
        'sku' => 'UNAUTH-001',
        'category_id' => $category->id,
        'price' => '19.99',
        'stock_quantity' => 5,
        'status' => ProductStatus::Published->value,
    ])->assertForbidden();
});

test('marketing manager cannot delete customers', function (): void {
    actingAsAdmin('marketing_manager');

    $customer = Customer::factory()->create();

    $this->delete(route('admin.customers.destroy', $customer))
        ->assertForbidden();
});

test('admin login is rate limited after repeated failures', function (): void {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('admin.login.store'), [
            'email' => 'owner@valorsupply.co',
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('admin.login.store'), [
        'email' => 'owner@valorsupply.co',
        'password' => 'wrong-password',
    ])->assertStatus(429);
});

test('order address html escapes xss payloads', function (): void {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'shipping_address' => [
            'first_name' => 'Test',
            'last_name' => 'User',
            'line1' => '<script>alert(1)</script>',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'billing_address' => [
            'first_name' => 'Test',
            'last_name' => 'User',
            'line1' => '<img src=x onerror=alert(1)>',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
    ]);

    $payload = OrderTrackingResource::make($order)->resolve();

    expect($payload['shipping_address']['html'])
        ->not->toContain('<script>')
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->and($payload['billing_address']['html'])
        ->toContain('&lt;img')
        ->not->toContain('<img');
});

test('account reviews route boots for authenticated customers', function (): void {
    actingAsCustomer();

    $this->get(route('account.reviews'))
        ->assertSuccessful();
});

test('customers with orders cannot be deleted', function (): void {
    actingAsAdmin();

    $customer = Customer::factory()->create();
    Order::factory()->create(['customer_id' => $customer->id]);

    $this->delete(route('admin.customers.destroy', $customer))
        ->assertSessionHasErrors('customer');

    expect(Customer::query()->find($customer->id))->not->toBeNull();
});

test('placed orders are marked paid only after payment capture', function (): void {
    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->post(route('checkout.store'), [
        'email' => 'paid-flow@example.com',
        'shipping' => [
            'first_name' => 'Pay',
            'last_name' => 'Flow',
            'line1' => '123 Main Street',
            'city' => 'Columbus',
            'state' => 'OH',
            'postal_code' => '43215',
            'country' => 'United States',
        ],
        'billing_same_as_shipping' => '1',
        'delivery_method' => 'standard',
        'payment_method' => 'card',
        'terms_accepted' => '1',
    ])->assertRedirect();

    $order = Order::query()->whereHas('customer', fn ($q) => $q->where('email', 'paid-flow@example.com'))->firstOrFail();

    expect($order->payment_status)->toBe(PaymentStatus::Paid);
});
