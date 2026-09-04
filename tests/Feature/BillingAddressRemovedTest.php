<?php

use App\Enums\AddressType;
use App\Models\Product;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('orders table has no billing_address column', function (): void {
    expect(Schema::hasColumn('orders', 'billing_address'))->toBeFalse();
});

test('address type only supports shipping', function (): void {
    expect(AddressType::cases())->toHaveCount(1)
        ->and(AddressType::Shipping->value)->toBe('shipping')
        ->and(AddressType::values())->toBe(['shipping']);
});

test('checkout does not show billing address', function (): void {
    $this->seed(CommerceSeeder::class);

    $product = Product::query()->published()->inStock()->firstOrFail();

    $this->postJson(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->get(route('checkout'))
        ->assertSuccessful()
        ->assertDontSee('Billing address')
        ->assertDontSee('billing_same_as_shipping');
});
