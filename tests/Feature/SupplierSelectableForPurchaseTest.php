<?php

use App\Enums\SupplierStatus;
use App\Models\Supplier;
use App\Services\Admin\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('only active suppliers are selectable for new purchases', function (): void {
    $active = Supplier::factory()->active()->create(['name' => 'Active Purchase Supplier']);
    $inactive = Supplier::factory()->inactive()->create(['name' => 'Inactive Purchase Supplier']);

    $selectable = app(SupplierService::class)->selectableForPurchase();

    expect($selectable->pluck('id')->all())
        ->toContain($active->id)
        ->not->toContain($inactive->id);

    expect($active->isSelectableForPurchase())->toBeTrue()
        ->and($inactive->isSelectableForPurchase())->toBeFalse();
});

test('inactive supplier cannot be asserted selectable for purchase', function (): void {
    $supplier = Supplier::factory()->inactive()->create();

    expect(fn () => app(SupplierService::class)->assertSelectableForPurchase($supplier))
        ->toThrow(ValidationException::class);
});

test('soft deleted suppliers remain in database for historical references', function (): void {
    $supplier = Supplier::factory()->create();
    $id = $supplier->id;

    app(SupplierService::class)->delete($supplier);

    expect(Supplier::query()->find($id))->toBeNull()
        ->and(Supplier::withTrashed()->find($id))->not->toBeNull()
        ->and(Supplier::withTrashed()->find($id)->trashed())->toBeTrue();
});

test('supplier purchase summary is ready for purchase management wiring', function (): void {
    $supplier = Supplier::factory()->create();
    $summary = app(SupplierService::class)->purchaseSummary($supplier);

    expect($summary->purchaseCount)->toBe(0)
        ->and($summary->totalPurchaseValueCents)->toBe(0)
        ->and($summary->outstandingPayableCents)->toBe(0)
        ->and($summary->lastPurchaseAt)->toBeNull()
        ->and($summary->productsPurchased)->toBe([]);
});

test('supplier status enum gates purchase selection', function (): void {
    expect(SupplierStatus::Active->canBeSelectedForPurchase())->toBeTrue()
        ->and(SupplierStatus::Inactive->canBeSelectedForPurchase())->toBeFalse();
});
