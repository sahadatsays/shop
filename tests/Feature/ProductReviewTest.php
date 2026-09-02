<?php

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('customer cannot review a product without a delivered order', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['status' => ProductStatus::Published]);

    actingAsCustomer($customer);

    $this->post(route('product.reviews.store', $product), [
        'rating' => 5,
        'title' => 'Great gear',
        'body' => 'Excellent quality and fast shipping.',
    ])->assertSessionHasErrors('product');
});

test('customer cannot review a product from a shipped but undelivered order', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['status' => ProductStatus::Published]);

    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => OrderStatus::Shipped,
        'placed_at' => now()->subDays(3),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price_cents' => $product->price_cents,
        'line_total_cents' => $product->price_cents,
    ]);

    actingAsCustomer($customer);

    $this->post(route('product.reviews.store', $product), [
        'rating' => 4,
        'title' => 'Almost there',
        'body' => 'Waiting for delivery before I can review properly.',
    ])->assertSessionHasErrors('product');
});

test('session customer id alone cannot access account routes', function (): void {
    $customer = Customer::factory()->create();

    $this->withSession(['customer_id' => $customer->id])
        ->get(route('account'))
        ->assertRedirect(route('login'));
});

test('customer cannot submit a review using session customer id without guard user', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->firstOrFail();

    createDeliveredOrderForCustomer($customer, $product);

    $this->withSession(['customer_id' => $customer->id])
        ->post(route('product.reviews.store', $product), [
            'rating' => 5,
            'title' => 'Session auth should fail',
            'body' => 'Submitted with session customer id only.',
            'redirect' => 'account',
        ])
        ->assertRedirect(route('login'));

    expect(Review::query()->where('customer_id', $customer->id)->where('product_id', $product->id)->exists())
        ->toBeFalse();
});

test('customer can submit a review after delivery', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->firstOrFail();

    createDeliveredOrderForCustomer($customer, $product);

    actingAsCustomer($customer);

    $this->post(route('product.reviews.store', $product), [
        'rating' => 5,
        'title' => 'Built to last',
        'body' => 'Exactly what I expected from a veteran-owned brand.',
        'redirect' => 'account',
        'write_product_id' => $product->id,
    ])->assertRedirect(route('account.reviews'));

    $review = Review::query()
        ->where('customer_id', $customer->id)
        ->where('product_id', $product->id)
        ->first();

    expect($review)->not->toBeNull()
        ->and($review->order_id)->not->toBeNull()
        ->and($review->is_approved)->toBeFalse()
        ->and($review->title)->toBe('Built to last');
});

test('customer cannot submit a second review for the same product', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->firstOrFail();
    $order = createDeliveredOrderForCustomer($customer, $product);

    Review::factory()->forCustomer($customer)->create([
        'product_id' => $product->id,
        'order_id' => $order->id,
    ]);

    actingAsCustomer($customer);

    $this->post(route('product.reviews.store', $product), [
        'rating' => 3,
        'title' => 'Duplicate',
        'body' => 'This should not be accepted.',
    ])->assertSessionHasErrors('product');
});

test('customer can update and delete their own review', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->firstOrFail();
    $order = createDeliveredOrderForCustomer($customer, $product);

    $review = Review::factory()->forCustomer($customer)->create([
        'product_id' => $product->id,
        'order_id' => $order->id,
        'title' => 'Original title',
        'body' => 'Original body.',
    ]);

    actingAsCustomer($customer);

    $this->patch(route('account.reviews.update', $review), [
        'rating' => 4,
        'title' => 'Updated title',
        'body' => 'Updated body with more detail.',
    ])->assertRedirect(route('account.reviews'));

    expect($review->fresh())
        ->rating->toBe(4)
        ->title->toBe('Updated title');

    $this->delete(route('account.reviews.destroy', $review))
        ->assertRedirect(route('account.reviews'));

    expect(Review::query()->find($review->id))->toBeNull();
});

test('customer cannot update another customers review', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $otherCustomer = Customer::query()->active()->whereKeyNot($customer->id)->firstOrFail();
    $product = Product::query()->published()->firstOrFail();
    $order = createDeliveredOrderForCustomer($otherCustomer, $product);

    $review = Review::factory()->forCustomer($otherCustomer)->create([
        'product_id' => $product->id,
        'order_id' => $order->id,
    ]);

    actingAsCustomer($customer);

    $this->patch(route('account.reviews.update', $review), [
        'rating' => 1,
        'title' => 'Hijacked',
        'body' => 'Should not work.',
    ])->assertForbidden();
});

test('product page displays approved customer reviews', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->firstOrFail();
    $order = createDeliveredOrderForCustomer($customer, $product);

    Review::factory()->forCustomer($customer)->create([
        'product_id' => $product->id,
        'order_id' => $order->id,
        'title' => 'Field tested',
        'body' => 'Holds up in all weather.',
        'is_approved' => true,
    ]);

    $this->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee('Field tested')
        ->assertSee('Holds up in all weather.')
        ->assertSee('Verified purchase');
});

test('account reviews page shows write review controls for delivered products', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $product = Product::query()->published()->firstOrFail();

    createDeliveredOrderForCustomer($customer, $product);

    actingAsCustomer($customer);

    $this->get(route('account.reviews'))
        ->assertSuccessful()
        ->assertSee('Ready for your review')
        ->assertSee($product->name)
        ->assertSee('data-review-write', false)
        ->assertSee('Write review');
});

test('product page shows write review form for eligible customer', function (): void {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['status' => \App\Enums\ProductStatus::Published]);

    createDeliveredOrderForCustomer($customer, $product);

    actingAsCustomer($customer);

    expect(auth('customer')->check())->toBeTrue()
        ->and(app(\App\Services\ProductReviewService::class)->canReview($customer, $product))->toBeTrue();

    $this->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee('Write a review', false)
        ->assertSee('Submit', false)
        ->assertSee(route('product.reviews.store', $product), false);
});

test('account reviews page lists customer reviews and reviewable products', function (): void {
    $customer = Customer::query()->active()->firstOrFail();
    $reviewedProduct = Product::query()->published()->firstOrFail();
    $pendingProduct = Product::query()->published()->whereKeyNot($reviewedProduct->id)->firstOrFail();

    $reviewedOrder = createDeliveredOrderForCustomer($customer, $reviewedProduct);
    createDeliveredOrderForCustomer($customer, $pendingProduct);

    Review::factory()->forCustomer($customer)->create([
        'product_id' => $reviewedProduct->id,
        'order_id' => $reviewedOrder->id,
        'title' => 'Already reviewed',
        'body' => 'Posted earlier.',
    ]);

    actingAsCustomer($customer);

    $this->get(route('account.reviews'))
        ->assertSuccessful()
        ->assertSee('Already reviewed')
        ->assertSee('Ready for your review')
        ->assertSee($pendingProduct->name);
});
