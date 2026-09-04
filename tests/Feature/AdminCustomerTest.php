<?php

use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\CustomerNote;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('customers index page renders with search and filters', function (): void {
    $customer = Customer::query()->firstOrFail();

    $this->get(route('admin.customers.index'))
        ->assertSuccessful()
        ->assertSee('Customers')
        ->assertSee($customer->name);

    $this->get(route('admin.customers.index', ['search' => $customer->email]))
        ->assertSuccessful()
        ->assertSee($customer->name);

    $this->get(route('admin.customers.index', ['status' => CustomerStatus::Active->value]))
        ->assertSuccessful();
});

test('customer can be created with address and note', function (): void {
    $this->post(route('admin.customers.store'), [
        'name' => 'Alex Rivera',
        'email' => 'alex.rivera@example.com',
        'phone' => '555-0100',
        'status' => CustomerStatus::Active->value,
        'internal_notes' => 'VIP customer',
        'note' => 'Welcomed during onboarding call.',
        'addresses' => [
            [
                'label' => 'Home',
                'type' => AddressType::Shipping->value,
                'name' => 'Alex Rivera',
                'line1' => '100 Valor Way',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '78701',
                'country' => 'US',
                'is_default' => true,
            ],
        ],
    ])->assertRedirect();

    $customer = Customer::query()->where('email', 'alex.rivera@example.com')->firstOrFail();

    expect($customer->name)->toBe('Alex Rivera')
        ->and($customer->status)->toBe(CustomerStatus::Active)
        ->and($customer->addresses)->toHaveCount(1)
        ->and($customer->notes)->toHaveCount(1)
        ->and($customer->addresses->first()->city)->toBe('Austin');
});

test('customer profile shows order history and addresses', function (): void {
    $customer = Customer::query()->has('orders')->withCount('orders')->firstOrFail();

    $this->get(route('admin.customers.show', $customer))
        ->assertSuccessful()
        ->assertSee($customer->name)
        ->assertSee($customer->email)
        ->assertSee('Order history')
        ->assertSee('Edit customer');
});

test('customer can be updated and note can be added from profile', function (): void {
    $customer = Customer::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'status' => CustomerStatus::Active,
    ]);

    $this->put(route('admin.customers.update', $customer), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'status' => CustomerStatus::Inactive->value,
        'phone' => '555-9999',
        'addresses' => [
            [
                'label' => 'Office',
                'type' => AddressType::Shipping->value,
                'name' => 'Updated Name',
                'line1' => '200 Main St',
                'city' => 'Dallas',
                'state' => 'TX',
                'postal_code' => '75201',
                'country' => 'US',
                'is_default' => true,
            ],
        ],
    ])->assertRedirect(route('admin.customers.show', $customer));

    $customer->refresh();

    expect($customer->name)->toBe('Updated Name')
        ->and($customer->status)->toBe(CustomerStatus::Inactive)
        ->and($customer->addresses)->toHaveCount(1);

    $this->post(route('admin.customers.notes.store', $customer), [
        'body' => 'Followed up about a return.',
    ])->assertRedirect(route('admin.customers.show', $customer));

    expect(CustomerNote::query()->where('customer_id', $customer->id)->count())->toBe(1);
});

test('customer can be soft deleted and restored', function (): void {
    $customer = Customer::factory()->create();

    $this->delete(route('admin.customers.destroy', $customer))
        ->assertRedirect(route('admin.customers.index'));

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);

    $this->post(route('admin.customers.restore', $customer->id))
        ->assertRedirect(route('admin.customers.index', ['trashed' => 1]));

    expect(Customer::query()->find($customer->id))->not->toBeNull();
});

test('customers nav and dashboard links are enabled', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Customers')
        ->assertSee(route('admin.customers.index'), false);
});
