<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\AdminUserService;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
});

test('admin users index lists seeded staff accounts', function (): void {
    actingAsAdmin();

    $this->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Jordan Reeves')
        ->assertSee('owner@valorsupply.co');
});

test('manager cannot access admin users without permission', function (): void {
    actingAsAdmin('manager');

    $this->get(route('admin.users.index'))->assertForbidden();
});

test('owner can create admin user with roles', function (): void {
    actingAsAdmin();

    $role = Role::query()->where('slug', 'customer_support')->firstOrFail();

    $this->post(route('admin.users.store'), [
        'name' => 'Support Agent',
        'email' => 'agent@valorsupply.co',
        'password' => 'password',
        'password_confirmation' => 'password',
        'is_active' => true,
        'roles' => [$role->id],
    ])->assertRedirect();

    $user = User::query()->where('email', 'agent@valorsupply.co')->firstOrFail();

    expect($user->roles->pluck('slug')->all())->toBe(['customer_support']);
});

test('owner can update admin user roles', function (): void {
    actingAsAdmin();

    $user = User::factory()->create(['email' => 'editor@valorsupply.co']);
    $supportRole = Role::query()->where('slug', 'customer_support')->firstOrFail();
    $user->roles()->sync([$supportRole->id]);

    $productRole = Role::query()->where('slug', 'product_manager')->firstOrFail();

    $this->put(route('admin.users.update', $user), [
        'name' => 'Catalog Editor',
        'email' => 'editor@valorsupply.co',
        'is_active' => true,
        'roles' => [$productRole->id],
    ])->assertRedirect(route('admin.users.show', $user));

    expect($user->fresh('roles')->roles->pluck('slug')->all())->toBe(['product_manager']);
});

test('admin user cannot delete their own account', function (): void {
    $owner = actingAsAdmin();

    $this->delete(route('admin.users.destroy', $owner))
        ->assertSessionHasErrors('user');

    expect(User::query()->whereKey($owner->id)->exists())->toBeTrue();
});

test('admin user cannot deactivate their own account', function (): void {
    $owner = actingAsAdmin();
    $ownerRole = Role::query()->where('slug', 'owner')->firstOrFail();

    $this->put(route('admin.users.update', $owner), [
        'name' => $owner->name,
        'email' => $owner->email,
        'is_active' => false,
        'roles' => [$ownerRole->id],
    ])->assertSessionHasErrors('is_active');
});

test('only owners can assign owner role', function (): void {
    $owner = actingAsAdmin();
    $ownerRole = Role::query()->where('slug', 'owner')->firstOrFail();

    $user = User::factory()->create(['email' => 'second@valorsupply.co']);
    $managerRole = Role::query()->where('slug', 'manager')->firstOrFail();
    $user->roles()->sync([$managerRole->id]);

    $this->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'is_active' => true,
        'roles' => [$ownerRole->id],
    ])->assertRedirect();

    expect($user->fresh('roles')->hasRole('owner'))->toBeTrue();
});

test('non-owner cannot assign owner role', function (): void {
    $permission = Permission::query()->where('slug', 'users.manage')->firstOrFail();
    $managerRole = Role::query()->where('slug', 'manager')->firstOrFail();
    $managerRole->permissions()->syncWithoutDetaching([$permission->id]);

    $manager = User::query()
        ->whereHas('roles', fn ($query) => $query->where('slug', 'manager'))
        ->with('roles.permissions')
        ->firstOrFail();

    $target = User::factory()->create(['email' => 'target@valorsupply.co']);
    $target->roles()->sync([$managerRole->id]);
    $ownerRole = Role::query()->where('slug', 'owner')->firstOrFail();

    $service = app(AdminUserService::class);

    expect(fn () => $service->update($target, [
        'name' => $target->name,
        'email' => $target->email,
        'is_active' => true,
        'roles' => [$ownerRole->id],
    ], $manager))->toThrow(ValidationException::class);

    expect($target->fresh('roles')->hasRole('owner'))->toBeFalse();
});

test('last active owner cannot be removed or deactivated', function (): void {
    actingAsAdmin();

    $owner = User::query()->where('email', 'owner@valorsupply.co')->firstOrFail();
    $managerRole = Role::query()->where('slug', 'manager')->firstOrFail();

    $this->put(route('admin.users.update', $owner), [
        'name' => $owner->name,
        'email' => $owner->email,
        'is_active' => true,
        'roles' => [$managerRole->id],
    ])->assertSessionHasErrors('roles');
});
