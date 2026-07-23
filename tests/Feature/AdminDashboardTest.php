<?php

use App\Support\Admin\Navigation\NavRegistry;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(CommerceSeeder::class);
    actingAsAdmin();
});

test('admin dashboard renders successfully with live data', function (): void {
    $response = $this->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('Dashboard');
    $response->assertSee('Recent Orders');
    $response->assertSee('Latest Customers');
    $response->assertSee('Top Products');
    $response->assertSee('Low Stock Alerts');
    $response->assertSee('Quick Actions');
    $response->assertSee('Sales by Month');
    $response->assertSee('data-admin-shell', false);
    $response->assertSee('dashboardCharts', false);
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
