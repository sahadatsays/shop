<?php

use App\Services\Admin\DashboardService;
use Database\Seeders\CommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CommerceSeeder::class);
});

test('dashboard service returns all required widgets', function (): void {
    $dashboard = app(DashboardService::class)->getViewData();

    expect($dashboard->stats)->toHaveCount(7)
        ->and($dashboard->charts)->toHaveCount(5)
        ->and($dashboard->recentOrders)->not->toBeEmpty()
        ->and($dashboard->latestCustomers)->not->toBeEmpty()
        ->and($dashboard->topProducts)->not->toBeEmpty()
        ->and($dashboard->quickActions)->toHaveCount(4);

    $labels = collect($dashboard->stats)->pluck('label')->all();

    expect($labels)->toContain("Today's Sales")
        ->toContain("Today's Orders")
        ->toContain('Revenue')
        ->toContain('Customers')
        ->toContain('Products')
        ->toContain('Pending Orders')
        ->toContain('Low Stock');
});

test('dashboard charts include monthly and inventory datasets', function (): void {
    $charts = collect(app(DashboardService::class)->getViewData()->charts);

    expect($charts->pluck('id')->all())->toBe([
        'sales-by-month',
        'orders-by-month',
        'revenue-by-month',
        'top-categories',
        'inventory-status',
    ]);

    $salesChart = $charts->firstWhere('id', 'sales-by-month');

    expect($salesChart->labels)->toHaveCount(12)
        ->and($salesChart->series[0]['data'])->toHaveCount(12);
});
