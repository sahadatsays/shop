<?php

use App\Models\StoreSetting;
use App\Support\StoreSettings;
use Database\Seeders\AdminAccessSeeder;
use Database\Seeders\StoreSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(AdminAccessSeeder::class);
    $this->seed(StoreSettingsSeeder::class);
    actingAsAdmin();
});

test('store settings page renders for authorized admin', function (): void {
    $settings = StoreSettings::current();

    $this->get(route('admin.settings.edit'))
        ->assertSuccessful()
        ->assertSee('Store Settings')
        ->assertSee('Store information')
        ->assertSee($settings->store_name);
});

test('store settings can be updated', function (): void {
    $this->put(route('admin.settings.update'), [
        'store_name' => 'Valor Supply Test',
        'tagline' => 'Built to serve.',
        'description' => 'Updated store description.',
        'email' => 'hello@test.com',
        'support_email' => 'support@test.com',
        'phone' => '555-0100',
        'address' => '123 Test Street',
        'social_instagram' => 'https://instagram.com/test',
        'social_facebook' => null,
        'social_youtube' => null,
        'social_x' => null,
        'currency' => 'USD',
        'timezone' => 'America/Chicago',
        'mail_from_name' => 'Valor Test',
        'mail_from_address' => 'noreply@test.com',
        'maintenance_enabled' => false,
        'maintenance_message' => null,
        'maintenance_secret' => null,
        'meta_title' => 'Valor Test Store',
        'meta_description' => 'Test meta description.',
        'meta_keywords' => 'test, gear',
        'utility_bar_message' => 'Test announcement bar.',
        'free_shipping_threshold' => '99.00',
        'google_analytics_id' => null,
        'theme_colors' => StoreSetting::defaultThemeColors(),
    ])->assertRedirect(route('admin.settings.edit'));

    StoreSettings::clearCache();

    $settings = StoreSettings::current();

    expect($settings->store_name)->toBe('Valor Supply Test')
        ->and($settings->tagline)->toBe('Built to serve.')
        ->and($settings->timezone)->toBe('America/Chicago')
        ->and($settings->utility_bar_message)->toBe('Test announcement bar.')
        ->and($settings->free_shipping_threshold_cents)->toBe(9900);
});

test('theme css variables enforce readable header text on dark backgrounds', function (): void {
    $settings = StoreSetting::query()->firstOrFail();
    $settings->update([
        'theme_colors' => array_merge(StoreSetting::defaultThemeColors(), [
            'header_bg' => '#090f1d',
            'header_text' => '#0f172a',
        ]),
    ]);

    $variables = $settings->fresh()->themeCssVariables();

    expect($variables['--store-header-text'])->toBe('#e2e8f0')
        ->and(StoreSetting::contrastRatio('#090f1d', $variables['--store-header-text']))->toBeGreaterThan(4.5);
});

test('storefront layout uses configured store settings', function (): void {
    StoreSetting::query()->first()?->update([
        'store_name' => 'Configured Store Name',
        'utility_bar_message' => 'Configured utility bar message.',
        'support_email' => 'configured@example.com',
    ]);

    StoreSettings::clearCache();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Configured Store Name')
        ->assertSee('Configured utility bar message.')
        ->assertSee('configured@example.com');
});

test('store settings require authentication', function (): void {
    auth('admin')->logout();

    $this->get(route('admin.settings.edit'))->assertRedirect(route('admin.login'));
});

test('customer support role cannot manage store settings', function (): void {
    auth('admin')->logout();
    actingAsAdmin('customer_support');

    $this->put(route('admin.settings.update'), [
        'store_name' => 'Blocked Update',
        'currency' => 'USD',
        'timezone' => 'UTC',
    ])->assertForbidden();
});
