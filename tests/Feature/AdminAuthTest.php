<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
});

test('guest is redirected from admin dashboard to login', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

test('admin login page renders', function (): void {
    $this->get(route('admin.login'))
        ->assertSuccessful()
        ->assertSee('Admin sign in')
        ->assertSee('owner@valorsupply.co');
});

test('admin can sign in with valid credentials', function (): void {
    $this->post(route('admin.login.store'), [
        'email' => 'owner@valorsupply.co',
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs(User::query()->where('email', 'owner@valorsupply.co')->firstOrFail(), 'admin');
});

test('invalid admin credentials are rejected', function (): void {
    $this->post(route('admin.login.store'), [
        'email' => 'owner@valorsupply.co',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest('admin');
});

test('admin can sign out', function (): void {
    actingAsAdmin();

    $this->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest('admin');
});

test('customer support role cannot access permission matrix', function (): void {
    actingAsAdmin('customer_support');

    $this->get(route('admin.roles.matrix'))->assertForbidden();
});

test('owner can access roles and permission matrix', function (): void {
    actingAsAdmin('owner');

    $this->get(route('admin.roles.index'))
        ->assertSuccessful()
        ->assertSee('Roles');

    $this->get(route('admin.roles.matrix'))
        ->assertSuccessful()
        ->assertSee('Permission Matrix');
});
