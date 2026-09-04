<?php

use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('inventory index page renders with stock summary', function (): void {
    $this->get(route('admin.inventory.index'))
        ->assertSuccessful()
        ->assertSee('Inventory')
        ->assertSee('In stock')
        ->assertSee('Low stock')
        ->assertSee('Out of stock');
});

test('inventory show page displays warehouse stock and movements', function (): void {
    $product = Product::query()->where('stock_quantity', '>', 0)->firstOrFail();

    $this->get(route('admin.inventory.show', $product))
        ->assertSuccessful()
        ->assertSee($product->name)
        ->assertSee('Warehouse stock')
        ->assertSee('Dhaka Central Warehouse');
});

test('stock can be increased via adjustment', function (): void {
    $product = Product::query()->where('stock_quantity', '>', 0)->firstOrFail();
    $warehouse = Warehouse::query()->where('is_default', true)->firstOrFail();
    $before = $product->stock_quantity;

    $this->post(route('admin.inventory.adjust.store', $product), [
        'warehouse_id' => $warehouse->id,
        'type' => StockMovementType::AdjustmentIn->value,
        'quantity' => 5,
        'notes' => 'Received shipment',
    ])->assertRedirect(route('admin.inventory.show', $product));

    $product->refresh();

    expect($product->stock_quantity)->toBe($before + 5);

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'type' => StockMovementType::AdjustmentIn->value,
        'quantity_change' => 5,
    ]);
});

test('stock can be decreased via adjustment', function (): void {
    $product = Product::query()->where('stock_quantity', '>', 5)->firstOrFail();
    $warehouse = Warehouse::query()->where('is_default', true)->firstOrFail();
    $before = $product->stock_quantity;

    $this->post(route('admin.inventory.adjust.store', $product), [
        'warehouse_id' => $warehouse->id,
        'type' => StockMovementType::AdjustmentOut->value,
        'quantity' => 3,
        'notes' => 'Damaged units',
    ])->assertRedirect(route('admin.inventory.show', $product));

    $product->refresh();

    expect($product->stock_quantity)->toBe($before - 3);
});

test('stock recount sets exact warehouse quantity', function (): void {
    $product = Product::query()->where('stock_quantity', '>', 0)->firstOrFail();
    $warehouse = Warehouse::query()->where('is_default', true)->firstOrFail();

    $this->post(route('admin.inventory.adjust.store', $product), [
        'warehouse_id' => $warehouse->id,
        'type' => StockMovementType::Recount->value,
        'quantity' => 42,
        'reference' => 'CC-2026-01',
    ])->assertRedirect(route('admin.inventory.show', $product));

    $product->refresh();

    expect($product->stock_quantity)->toBe(42);

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $product->id,
        'type' => StockMovementType::Recount->value,
        'quantity_after' => 42,
        'reference' => 'CC-2026-01',
    ]);
});

test('product creation logs initial stock movement', function (): void {
    $category = Category::query()->firstOrFail();

    $this->post(route('admin.products.store'), [
        'name' => 'Inventory Test Product',
        'sku' => 'INV-TEST-001',
        'category_id' => $category->id,
        'price' => '29.99',
        'stock_quantity' => 15,
        'status' => ProductStatus::Published->value,
    ])->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('sku', 'INV-TEST-001')->firstOrFail();

    expect($product->stock_quantity)->toBe(15)
        ->and(StockMovement::query()->where('product_id', $product->id)->where('type', StockMovementType::Initial)->exists())->toBeTrue();
});

test('stock history page renders movements', function (): void {
    expect(StockMovement::query()->count())->toBeGreaterThan(0);

    $this->get(route('admin.inventory.movements'))
        ->assertSuccessful()
        ->assertSee('Stock history')
        ->assertSee('Initial stock');
});

test('inventory nav link is enabled', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Inventory');
});
