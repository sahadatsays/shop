<?php

use App\Enums\PurchaseStatus;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\Purchase;
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

function createPurchasePayload(Supplier $supplier, Product $product, int $quantity = 10, float $unitCost = 10): array
{
    return [
        'supplier_id' => $supplier->id,
        'purchase_date' => now()->toDateString(),
        'expected_delivery_date' => now()->addDays(5)->toDateString(),
        'notes' => 'Test purchase',
        'discount_cents' => 0,
        'shipping_cents' => 1,
        'tax_cents' => 0.5,
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost_cents' => $unitCost,
                'discount_cents' => 0,
                'tax_cents' => 0,
            ],
        ],
    ];
}

test('purchase can be created without increasing stock', function (): void {
    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();
    $initialStock = $product->stock_quantity;
    $sellingPrice = $product->price_cents;

    $this->post(route('admin.purchases.store'), createPurchasePayload($supplier, $product, 25, 15))
        ->assertRedirect();

    $purchase = Purchase::query()->firstOrFail();

    expect($purchase->status)->toBe(PurchaseStatus::Draft)
        ->and($purchase->purchase_number)->toStartWith('PUR-')
        ->and($purchase->grand_total_cents)->toBe((int) round(((25 * 15) + 1 + 0.5) * 100))
        ->and($purchase->items)->toHaveCount(1)
        ->and($purchase->items->first()->unit_cost_cents)->toBe(1500)
        ->and($product->fresh()->stock_quantity)->toBe($initialStock)
        ->and($product->fresh()->price_cents)->toBe($sellingPrice)
        ->and(StockMovement::query()->where('type', StockMovementType::Purchase)->count())->toBe(0);
});

test('draft purchase can be submitted and approved', function (): void {
    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();

    $this->post(route('admin.purchases.store'), createPurchasePayload($supplier, $product))->assertRedirect();
    $purchase = Purchase::query()->firstOrFail();

    $this->post(route('admin.purchases.submit', $purchase))
        ->assertRedirect(route('admin.purchases.show', $purchase));

    expect($purchase->fresh()->status)->toBe(PurchaseStatus::Submitted);

    $this->post(route('admin.purchases.approve', $purchase))
        ->assertRedirect(route('admin.purchases.show', $purchase));

    expect($purchase->fresh()->status)->toBe(PurchaseStatus::Approved)
        ->and($purchase->fresh()->approved_by)->not->toBeNull();
});

test('draft purchase can be cancelled without inventory impact', function (): void {
    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();
    $initialStock = $product->stock_quantity;

    $this->post(route('admin.purchases.store'), createPurchasePayload($supplier, $product))->assertRedirect();
    $purchase = Purchase::query()->firstOrFail();

    $this->post(route('admin.purchases.cancel', $purchase))->assertRedirect();

    expect($purchase->fresh()->status)->toBe(PurchaseStatus::Cancelled)
        ->and($product->fresh()->stock_quantity)->toBe($initialStock);
});

test('inactive supplier cannot be used for new purchases', function (): void {
    $supplier = Supplier::factory()->inactive()->create();
    $product = Product::query()->published()->firstOrFail();

    $this->from(route('admin.purchases.create'))
        ->post(route('admin.purchases.store'), createPurchasePayload($supplier, $product))
        ->assertRedirect(route('admin.purchases.create'))
        ->assertSessionHasErrors('supplier_id');
});

test('purchase manager can create but cannot approve or receive', function (): void {
    actingAsAdmin('purchase_manager');

    $this->get(route('admin.purchases.create'))->assertSuccessful();
    $this->get(route('admin.purchases.index'))->assertSuccessful();

    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();
    $this->post(route('admin.purchases.store'), createPurchasePayload($supplier, $product))->assertRedirect();

    $purchase = Purchase::query()->firstOrFail();
    $this->post(route('admin.purchases.approve', $purchase))->assertForbidden();
    $this->post(route('admin.purchases.receive', $purchase), [
        'warehouse_id' => Warehouse::query()->where('is_default', true)->value('id'),
        'idempotency_key' => (string) Str::uuid(),
        'items' => [],
    ])->assertForbidden();
});

test('marketing manager cannot view purchases', function (): void {
    actingAsAdmin('marketing_manager');

    $this->get(route('admin.purchases.index'))->assertForbidden();
});

test('supplier detail shows purchase history', function (): void {
    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();

    $this->post(route('admin.purchases.store'), createPurchasePayload($supplier, $product))->assertRedirect();
    $purchase = Purchase::query()->firstOrFail();

    $this->get(route('admin.suppliers.show', $supplier))
        ->assertSuccessful()
        ->assertSee($purchase->purchase_number)
        ->assertSee('Purchase history');
});

test('supplier with purchase history cannot be deleted', function (): void {
    $supplier = Supplier::factory()->create();
    $product = Product::query()->published()->firstOrFail();

    $this->post(route('admin.purchases.store'), createPurchasePayload($supplier, $product))->assertRedirect();

    $this->from(route('admin.suppliers.show', $supplier))
        ->delete(route('admin.suppliers.destroy', $supplier))
        ->assertRedirect()
        ->assertSessionHasErrors('supplier');
});
