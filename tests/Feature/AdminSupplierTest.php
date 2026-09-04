<?php

use App\Enums\SupplierStatus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Supplier;
use Database\Seeders\AdminAccessSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    actingAsAdmin();
});

test('suppliers index lists suppliers', function (): void {
    $supplier = Supplier::factory()->create([
        'name' => 'Alpha Feed Co',
        'company_name' => 'Alpha Feeds Ltd',
    ]);

    $this->get(route('admin.suppliers.index'))
        ->assertSuccessful()
        ->assertSee('Suppliers')
        ->assertSee($supplier->name)
        ->assertSee('Alpha Feeds Ltd');
});

test('suppliers index filters by status', function (): void {
    $active = Supplier::factory()->active()->create(['name' => 'Active Supplier Co']);
    $inactive = Supplier::factory()->inactive()->create(['name' => 'Inactive Supplier Co']);

    $this->get(route('admin.suppliers.index', ['status' => SupplierStatus::Active->value]))
        ->assertSuccessful()
        ->assertSee($active->name)
        ->assertDontSee($inactive->name);

    $this->get(route('admin.suppliers.index', ['status' => SupplierStatus::Inactive->value]))
        ->assertSuccessful()
        ->assertSee($inactive->name)
        ->assertDontSee($active->name);
});

test('suppliers index searches by name company phone and email', function (): void {
    $match = Supplier::factory()->create([
        'name' => 'Phoenix Vet Supplies',
        'company_name' => 'Phoenix Holdings',
        'phone' => '01711112222',
        'email' => 'phoenix-unique@example.com',
    ]);

    $other = Supplier::factory()->create([
        'name' => 'Seattle Pet Traders',
        'company_name' => 'Seattle Holdings',
        'phone' => '01899998888',
        'email' => 'seattle-unique@example.com',
    ]);

    foreach (['Phoenix', 'Phoenix Holdings', '01711112222', 'phoenix-unique@example.com'] as $term) {
        $this->get(route('admin.suppliers.index', ['search' => $term]))
            ->assertSuccessful()
            ->assertSee($match->name)
            ->assertDontSee($other->name);
    }
});

test('supplier can be created', function (): void {
    $this->post(route('admin.suppliers.store'), [
        'name' => 'Dhaka Agro Supplies',
        'company_name' => 'Dhaka Agro Supplies Ltd',
        'contact_person' => 'Rahim Ahmed',
        'phone' => '01700000001',
        'email' => 'Orders@DhakaAgro.Example',
        'address' => '12 Warehouse Road',
        'city' => 'Dhaka',
        'district' => 'Dhaka',
        'country' => 'Bangladesh',
        'tax_id' => 'TIN-12345678',
        'notes' => 'Net 30 terms',
        'status' => SupplierStatus::Active->value,
    ])->assertRedirect();

    $this->assertDatabaseHas('suppliers', [
        'name' => 'Dhaka Agro Supplies',
        'company_name' => 'Dhaka Agro Supplies Ltd',
        'email' => 'orders@dhakaagro.example',
        'status' => SupplierStatus::Active->value,
    ]);
});

test('supplier can be updated', function (): void {
    $supplier = Supplier::factory()->create([
        'name' => 'Old Supplier Name',
        'status' => SupplierStatus::Active,
    ]);

    $this->put(route('admin.suppliers.update', $supplier), [
        'name' => 'Updated Supplier Name',
        'company_name' => $supplier->company_name,
        'contact_person' => $supplier->contact_person,
        'phone' => $supplier->phone,
        'email' => $supplier->email,
        'address' => $supplier->address,
        'city' => $supplier->city,
        'district' => $supplier->district,
        'country' => $supplier->country,
        'tax_id' => $supplier->tax_id,
        'notes' => 'Updated notes',
        'status' => SupplierStatus::Inactive->value,
    ])->assertRedirect(route('admin.suppliers.show', $supplier));

    expect($supplier->fresh())
        ->name->toBe('Updated Supplier Name')
        ->status->toBe(SupplierStatus::Inactive)
        ->notes->toBe('Updated notes');
});

test('supplier can be soft deleted', function (): void {
    $supplier = Supplier::factory()->create();

    $this->delete(route('admin.suppliers.destroy', $supplier))
        ->assertRedirect(route('admin.suppliers.index'));

    $this->assertSoftDeleted($supplier);
});

test('supplier show page renders details and purchase summary placeholders', function (): void {
    $supplier = Supplier::factory()->create([
        'name' => 'Detail View Supplier',
        'company_name' => 'Detail View Ltd',
    ]);

    $this->get(route('admin.suppliers.show', $supplier))
        ->assertSuccessful()
        ->assertSee('Detail View Supplier')
        ->assertSee('Detail View Ltd')
        ->assertSee('Purchasing summary')
        ->assertSee('Purchase history')
        ->assertSee('No purchases yet');
});

test('marketing manager cannot view suppliers', function (): void {
    actingAsAdmin('marketing_manager');

    $this->get(route('admin.suppliers.index'))
        ->assertForbidden();
});

test('inventory manager can manage suppliers', function (): void {
    actingAsAdmin('inventory_manager');

    $this->get(route('admin.suppliers.index'))
        ->assertSuccessful();

    $this->get(route('admin.suppliers.create'))
        ->assertSuccessful();
});

test('create permission is required to create suppliers', function (): void {
    actingAsAdmin('marketing_manager');

    $this->post(route('admin.suppliers.store'), [
        'name' => 'Unauthorized Supplier',
        'status' => SupplierStatus::Active->value,
    ])->assertForbidden();
});

test('purchase history section requires purchases view permission', function (): void {
    $role = Role::query()->where('slug', 'customer_support')->firstOrFail();
    $permission = Permission::query()->where('slug', 'suppliers.view')->firstOrFail();
    $role->permissions()->syncWithoutDetaching([$permission->id]);

    actingAsAdmin('customer_support');

    $supplier = Supplier::factory()->create(['name' => 'Restricted Purchase View']);

    $this->get(route('admin.suppliers.show', $supplier))
        ->assertSuccessful()
        ->assertSee('Restricted Purchase View')
        ->assertDontSee('Purchasing summary')
        ->assertDontSee('Purchase history');
});
