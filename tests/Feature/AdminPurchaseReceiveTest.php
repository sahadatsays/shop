<?php

use App\Enums\ProductStatus;
use App\Enums\PurchaseStatus;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReceipt;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

function approvedPurchase(int $quantity = 100, int $unitCost = 1000): array
{
    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();
    $initialStock = $product->stock_quantity;
    $sellingPrice = $product->price_cents;

    actingAsAdmin();
    test()->post(route('admin.purchases.store'), [
        'supplier_id' => $supplier->id,
        'purchase_date' => now()->toDateString(),
        'discount_cents' => 0,
        'shipping_cents' => 0,
        'tax_cents' => 0,
        'items' => [[
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_cost_cents' => $unitCost,
            'discount_cents' => 0,
            'tax_cents' => 0,
        ]],
    ])->assertRedirect();

    $purchase = Purchase::query()->firstOrFail();
    test()->post(route('admin.purchases.approve', $purchase))->assertRedirect();

    return [
        'purchase' => $purchase->fresh(['items']),
        'product' => $product->fresh(),
        'initialStock' => $initialStock,
        'sellingPrice' => $sellingPrice,
        'warehouse' => Warehouse::query()->where('is_default', true)->firstOrFail(),
    ];
}

test('partial receiving increases inventory only by received quantity', function (): void {
    ['purchase' => $purchase, 'product' => $product, 'initialStock' => $initialStock, 'sellingPrice' => $sellingPrice, 'warehouse' => $warehouse] = approvedPurchase(100, 1200);
    $item = $purchase->items->first();

    $this->post(route('admin.purchases.receive', $purchase), [
        'warehouse_id' => $warehouse->id,
        'idempotency_key' => (string) Str::uuid(),
        'items' => [
            ['purchase_item_id' => $item->id, 'quantity' => 60],
        ],
    ])->assertRedirect(route('admin.purchases.show', $purchase));

    $purchase->refresh()->load('items');
    $product->refresh();

    expect($purchase->status)->toBe(PurchaseStatus::PartiallyReceived)
        ->and($purchase->items->first()->quantity_received)->toBe(60)
        ->and($purchase->items->first()->quantityRemaining())->toBe(40)
        ->and($product->stock_quantity)->toBe($initialStock + 60)
        ->and($product->price_cents)->toBe($sellingPrice)
        ->and($product->cost_cents)->toBe(1200);

    $movement = StockMovement::query()
        ->where('product_id', $product->id)
        ->where('type', StockMovementType::Purchase)
        ->firstOrFail();

    expect($movement->quantity_change)->toBe(60)
        ->and($movement->reference)->toBe($purchase->purchase_number);
});

test('full receiving completes the purchase', function (): void {
    ['purchase' => $purchase, 'product' => $product, 'initialStock' => $initialStock, 'warehouse' => $warehouse] = approvedPurchase(40, 800);
    $item = $purchase->items->first();

    $this->post(route('admin.purchases.receive', $purchase), [
        'warehouse_id' => $warehouse->id,
        'idempotency_key' => (string) Str::uuid(),
        'items' => [
            ['purchase_item_id' => $item->id, 'quantity' => 40],
        ],
    ])->assertRedirect();

    expect($purchase->fresh()->status)->toBe(PurchaseStatus::Completed)
        ->and($product->fresh()->stock_quantity)->toBe($initialStock + 40)
        ->and($purchase->fresh()->items->first()->quantityRemaining())->toBe(0);
});

test('multiple partial receives can complete a purchase', function (): void {
    ['purchase' => $purchase, 'product' => $product, 'initialStock' => $initialStock, 'warehouse' => $warehouse] = approvedPurchase(100, 500);
    $item = $purchase->items->first();

    $this->post(route('admin.purchases.receive', $purchase), [
        'warehouse_id' => $warehouse->id,
        'idempotency_key' => (string) Str::uuid(),
        'items' => [['purchase_item_id' => $item->id, 'quantity' => 60]],
    ])->assertRedirect();

    $this->post(route('admin.purchases.receive', $purchase), [
        'warehouse_id' => $warehouse->id,
        'idempotency_key' => (string) Str::uuid(),
        'items' => [['purchase_item_id' => $item->id, 'quantity' => 40]],
    ])->assertRedirect();

    expect($purchase->fresh()->status)->toBe(PurchaseStatus::Completed)
        ->and($product->fresh()->stock_quantity)->toBe($initialStock + 100)
        ->and(StockMovement::query()->where('type', StockMovementType::Purchase)->count())->toBe(2);
});

test('cannot receive more than remaining quantity', function (): void {
    ['purchase' => $purchase, 'warehouse' => $warehouse] = approvedPurchase(10, 500);
    $item = $purchase->items->first();

    $this->from(route('admin.purchases.show', $purchase))
        ->post(route('admin.purchases.receive', $purchase), [
            'warehouse_id' => $warehouse->id,
            'idempotency_key' => (string) Str::uuid(),
            'items' => [['purchase_item_id' => $item->id, 'quantity' => 11]],
        ])
        ->assertRedirect(route('admin.purchases.show', $purchase))
        ->assertSessionHasErrors('items');
});

test('cannot receive draft or cancelled purchases', function (): void {
    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();
    $warehouse = Warehouse::query()->where('is_default', true)->firstOrFail();

    $this->post(route('admin.purchases.store'), [
        'supplier_id' => $supplier->id,
        'purchase_date' => now()->toDateString(),
        'discount_cents' => 0,
        'shipping_cents' => 0,
        'tax_cents' => 0,
        'items' => [[
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_cost_cents' => 100,
            'discount_cents' => 0,
            'tax_cents' => 0,
        ]],
    ])->assertRedirect();

    $purchase = Purchase::query()->firstOrFail();
    $item = $purchase->items()->firstOrFail();

    $this->from(route('admin.purchases.show', $purchase))
        ->post(route('admin.purchases.receive', $purchase), [
            'warehouse_id' => $warehouse->id,
            'idempotency_key' => (string) Str::uuid(),
            'items' => [['purchase_item_id' => $item->id, 'quantity' => 1]],
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('purchase');

    $this->post(route('admin.purchases.cancel', $purchase))->assertRedirect();

    $this->from(route('admin.purchases.show', $purchase))
        ->post(route('admin.purchases.receive', $purchase), [
            'warehouse_id' => $warehouse->id,
            'idempotency_key' => (string) Str::uuid(),
            'items' => [['purchase_item_id' => $item->id, 'quantity' => 1]],
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('purchase');
});

test('duplicate receive submission is idempotent', function (): void {
    ['purchase' => $purchase, 'product' => $product, 'initialStock' => $initialStock, 'warehouse' => $warehouse] = approvedPurchase(20, 400);
    $item = $purchase->items->first();
    $key = (string) Str::uuid();

    $payload = [
        'warehouse_id' => $warehouse->id,
        'idempotency_key' => $key,
        'items' => [['purchase_item_id' => $item->id, 'quantity' => 5]],
    ];

    $this->post(route('admin.purchases.receive', $purchase), $payload)->assertRedirect();
    $this->post(route('admin.purchases.receive', $purchase), $payload)->assertRedirect();

    expect(PurchaseReceipt::query()->where('idempotency_key', $key)->count())->toBe(1)
        ->and($product->fresh()->stock_quantity)->toBe($initialStock + 5)
        ->and(StockMovement::query()->where('type', StockMovementType::Purchase)->count())->toBe(1);
});

test('archived product can still be received on an approved purchase', function (): void {
    ['purchase' => $purchase, 'product' => $product, 'initialStock' => $initialStock, 'warehouse' => $warehouse] = approvedPurchase(8, 250);
    $item = $purchase->items->first();

    $product->update(['status' => ProductStatus::Archived]);

    $this->post(route('admin.purchases.receive', $purchase), [
        'warehouse_id' => $warehouse->id,
        'idempotency_key' => (string) Str::uuid(),
        'items' => [['purchase_item_id' => $item->id, 'quantity' => 8]],
    ])->assertRedirect();

    expect($product->fresh()->stock_quantity)->toBe($initialStock + 8)
        ->and($purchase->fresh()->status)->toBe(PurchaseStatus::Completed);
});

test('inventory manager can receive stock', function (): void {
    ['purchase' => $purchase, 'warehouse' => $warehouse] = approvedPurchase(5, 100);
    $item = $purchase->items->first();

    actingAsAdmin('inventory_manager');

    $this->post(route('admin.purchases.receive', $purchase), [
        'warehouse_id' => $warehouse->id,
        'idempotency_key' => (string) Str::uuid(),
        'items' => [['purchase_item_id' => $item->id, 'quantity' => 5]],
    ])->assertRedirect(route('admin.purchases.show', $purchase));

    expect($purchase->fresh()->status)->toBe(PurchaseStatus::Completed);
});

test('zero quantity receive is rejected', function (): void {
    ['purchase' => $purchase, 'warehouse' => $warehouse] = approvedPurchase(5, 100);
    $item = $purchase->items->first();

    $this->from(route('admin.purchases.show', $purchase))
        ->post(route('admin.purchases.receive', $purchase), [
            'warehouse_id' => $warehouse->id,
            'idempotency_key' => (string) Str::uuid(),
            'items' => [['purchase_item_id' => $item->id, 'quantity' => 0]],
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('items');
});
