<?php

use App\Support\Admin\Navigation\NavRegistry;

test('admin dashboard renders successfully', function () {
    $response = $this->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('Dashboard');
    $response->assertSee('Recent orders');
    $response->assertSee('Skip to main content');
    $response->assertSee('data-admin-shell', false);
});

test('nav registry exposes dashboard navigation', function () {
    $items = NavRegistry::sidebar();

    expect($items)->not->toBeEmpty();
    expect($items[0]->label)->toBe('Dashboard');
    expect($items[0]->route)->toBe('admin.dashboard');
});

test('admin dashboard route is named correctly', function () {
    expect(route('admin.dashboard'))->toBe(url('/admin'));
});
