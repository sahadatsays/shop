<?php

use App\Support\Admin\Navigation\NavRegistry;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Database\Seeders\DashboardWidgetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(DashboardWidgetSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('admin dashboard renders the dynamic widget grid', function (): void {
    $response = $this->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('Dashboard');
    $response->assertSee('data-admin-shell', false);
    $response->assertSee('data-admin-dashboard', false);
    $response->assertSee('data-widget-grid', false);
    // Widget shell headers are rendered server-side (bodies load async).
    $response->assertSee('Recent Orders');
    $response->assertSee('Low Stock Alerts');
    $response->assertSee('Quick Actions');
    $response->assertSee('Sales Trend');
});

test('nav registry exposes dashboard navigation', function (): void {
    $items = NavRegistry::sidebar();

    expect($items)->not->toBeEmpty();
    expect($items[0]->label)->toBe('Dashboard');
    expect($items[0]->route)->toBe('admin.dashboard');
});

test('admin dashboard route is named correctly', function (): void {
    expect(route('admin.dashboard'))->toBe(url('/admin'));
});
