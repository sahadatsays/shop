<?php

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('warehouses index lists seeded warehouse', function (): void {
    $this->get(route('admin.warehouses.index'))
        ->assertSuccessful()
        ->assertSee('Warehouses')
        ->assertSee('Fort Worth Distribution Center')
        ->assertSee('FTW-01');
});

test('warehouse can be created', function (): void {
    $this->post(route('admin.warehouses.store'), [
        'name' => 'Dallas Fulfillment Hub',
        'code' => 'dal-02',
        'city' => 'Dallas',
        'state' => 'TX',
        'country' => 'US',
        'address' => '500 Commerce St',
        'is_active' => true,
        'is_default' => false,
        'sort_order' => 2,
    ])->assertRedirect();

    $this->assertDatabaseHas('warehouses', [
        'name' => 'Dallas Fulfillment Hub',
        'code' => 'DAL-02',
        'city' => 'Dallas',
    ]);
});

test('warehouse can be updated and default can be reassigned', function (): void {
    $currentDefault = Warehouse::query()->where('is_default', true)->firstOrFail();
    $warehouse = Warehouse::factory()->create([
        'name' => 'Austin Storage',
        'code' => 'AUS-01',
        'is_default' => false,
    ]);

    $this->put(route('admin.warehouses.update', $warehouse), [
        'name' => 'Austin Fulfillment',
        'code' => 'AUS-01',
        'city' => 'Austin',
        'state' => 'TX',
        'country' => 'US',
        'is_active' => true,
        'is_default' => true,
        'sort_order' => 1,
    ])->assertRedirect(route('admin.warehouses.show', $warehouse));

    expect($warehouse->fresh())
        ->name->toBe('Austin Fulfillment')
        ->is_default->toBeTrue();

    expect($currentDefault->fresh()->is_default)->toBeFalse();
});

test('warehouse with stock history cannot be deleted', function (): void {
    $warehouse = Warehouse::query()->where('code', 'FTW-01')->firstOrFail();

    $this->delete(route('admin.warehouses.destroy', $warehouse))
        ->assertSessionHasErrors('warehouse');

    expect(Warehouse::query()->whereKey($warehouse->id)->exists())->toBeTrue();
});

test('empty warehouse without history can be deleted', function (): void {
    $warehouse = Warehouse::factory()->create([
        'code' => 'TMP-01',
        'is_default' => false,
    ]);

    $this->delete(route('admin.warehouses.destroy', $warehouse))
        ->assertRedirect(route('admin.warehouses.index'));

    expect(Warehouse::query()->whereKey($warehouse->id)->exists())->toBeFalse();
});

test('warehouse with on-hand stock cannot be deleted', function (): void {
    $warehouse = Warehouse::factory()->create([
        'code' => 'STK-01',
        'is_default' => false,
    ]);

    WarehouseStock::query()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => Product::query()->firstOrFail()->id,
        'quantity' => 5,
    ]);

    $this->delete(route('admin.warehouses.destroy', $warehouse))
        ->assertSessionHasErrors('warehouse');
});

test('warehouse show page renders inventory summary', function (): void {
    $warehouse = Warehouse::query()->where('code', 'FTW-01')->firstOrFail();

    $this->get(route('admin.warehouses.show', $warehouse))
        ->assertSuccessful()
        ->assertSee($warehouse->name)
        ->assertSee('Total units')
        ->assertSee('Edit warehouse');
});
