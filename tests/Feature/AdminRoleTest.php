<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('roles index lists seeded system roles', function (): void {
    $this->get(route('admin.roles.index'))
        ->assertSuccessful()
        ->assertSee('Owner')
        ->assertSee('Customer Support');
});

test('custom role can be created with permissions', function (): void {
    $permissionIds = Permission::query()->whereIn('slug', ['products.view', 'products.manage'])->pluck('id')->all();

    $this->post(route('admin.roles.store'), [
        'name' => 'Catalog Editor',
        'description' => 'Can manage catalog content.',
        'permissions' => $permissionIds,
    ])->assertRedirect();

    $role = Role::query()->where('slug', 'catalog_editor')->firstOrFail();

    expect($role->permissions)->toHaveCount(2)
        ->and($role->is_system)->toBeFalse();
});

test('permission can be created and listed', function (): void {
    $this->post(route('admin.permissions.store'), [
        'name' => 'Export reports',
        'group' => 'Administration',
        'description' => 'Download operational reports.',
    ])->assertRedirect();

    $permission = Permission::query()->where('slug', 'export_reports')->firstOrFail();

    $this->get(route('admin.permissions.index'))
        ->assertSuccessful()
        ->assertSee('Export reports');

    expect($permission->group)->toBe('Administration');
});

test('permission matrix can be updated for a role', function (): void {
    $role = Role::query()->where('slug', 'customer_support')->firstOrFail();
    $permission = Permission::query()->where('slug', 'orders.manage')->firstOrFail();

    $this->put(route('admin.roles.matrix.update'), [
        'matrix' => [
            $role->id => [
                $permission->id => true,
            ],
        ],
    ])->assertRedirect(route('admin.roles.matrix'));

    expect($role->fresh('permissions')->permissions->contains('slug', 'orders.manage'))->toBeTrue();
});

test('system role cannot be deleted', function (): void {
    $role = Role::query()->where('slug', 'owner')->firstOrFail();

    $this->delete(route('admin.roles.destroy', $role))->assertStatus(422);
});
