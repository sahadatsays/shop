<?php

namespace App\Services\Admin;

use App\Models\StoreSetting;
use App\Support\StoreSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreSettingsService
{
    public function get(): StoreSetting
    {
        return StoreSettings::current();
    }

    /**
     * @return array<string, mixed>
     */
    public function formOptions(): array
    {
        return [
            'currencies' => [
                'USD' => 'USD — US Dollar',
                'EUR' => 'EUR — Euro',
                'GBP' => 'GBP — British Pound',
                'CAD' => 'CAD — Canadian Dollar',
                'AUD' => 'AUD — Australian Dollar',
                'BDT' => 'BDT — Bangladeshi Taka',
                'INR' => 'INR — Indian Rupee',
                'PKR' => 'PKR — Pakistani Rupee',
            ],
            // Provide a curated list of common primary timezones for quick selection.
            'timezones' => [
                'Asia/Dhaka',
                'Asia/Kolkata',     // India
                'Europe/London',    // UK
                'UTC',
                'America/New_York', // USA - Eastern
                'America/Chicago',  // USA - Central
                'America/Denver',   // USA - Mountain
                'America/Los_Angeles', // USA - Pacific
            ],
            'themeColorFields' => [
                'header_bg' => 'Header background',
                'header_text' => 'Header text',
                'utility_bar_bg' => 'Utility bar background',
                'utility_bar_text' => 'Utility bar text',
                'button_primary_bg' => 'Primary button background',
                'button_primary_text' => 'Primary button text',
                'button_accent_bg' => 'Accent button background',
                'button_accent_text' => 'Accent button text',
                'footer_bg' => 'Footer background',
                'link_accent' => 'Link accent color',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(
        array $data,
        ?UploadedFile $logo = null,
        ?UploadedFile $favicon = null,
        ?UploadedFile $ogImage = null,
    ): StoreSetting {
        $settings = $this->get();
        $wasMaintenanceEnabled = $settings->maintenance_enabled;

        $attributes = $this->prepareAttributes($data, $settings);

        if ($logo) {
            $this->deleteFile($settings->logo_path);
            $attributes['logo_path'] = $this->storeFile($logo, 'store/logo');
        }

        if ($favicon) {
            $this->deleteFile($settings->favicon_path);
            $attributes['favicon_path'] = $this->storeFile($favicon, 'store/favicon');
        }

        if ($ogImage) {
            $this->deleteFile($settings->og_image_path);
            $attributes['og_image_path'] = $this->storeFile($ogImage, 'store/og');
        }

        $settings->update($attributes);
        StoreSettings::clearCache();

        $settings->refresh();

        if ($wasMaintenanceEnabled !== $settings->maintenance_enabled) {
            $this->syncMaintenanceMode($settings);
        } elseif ($settings->maintenance_enabled) {
            $this->syncMaintenanceMode($settings);
        }

        $this->applyRuntimeConfig($settings);

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data, StoreSetting $settings): array
    {
        $themeColors = [];

        foreach (StoreSetting::defaultThemeColors() as $key => $default) {
            $value = $data['theme_colors'][$key] ?? $default;
            $themeColors[$key] = is_string($value) ? $value : $default;
        }

        return [
            'store_name' => $data['store_name'],
            'tagline' => $data['tagline'] ?? null,
            'description' => $data['description'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'support_email' => $data['support_email'] ?? null,
            'social_links' => [
                'instagram' => $data['social_instagram'] ?? null,
                'facebook' => $data['social_facebook'] ?? null,
                'youtube' => $data['social_youtube'] ?? null,
                'x' => $data['social_x'] ?? null,
            ],
            'currency' => strtoupper($data['currency']),
            'timezone' => $data['timezone'],
            'mail_from_name' => $data['mail_from_name'] ?? null,
            'mail_from_address' => $data['mail_from_address'] ?? null,
            'maintenance_enabled' => (bool) ($data['maintenance_enabled'] ?? false),
            'maintenance_message' => $data['maintenance_message'] ?? null,
            'maintenance_secret' => filled($data['maintenance_secret'] ?? null)
                ? $data['maintenance_secret']
                : ($settings->maintenance_secret ?? Str::lower(Str::random(12))),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'utility_bar_message' => $data['utility_bar_message'] ?? null,
            'free_shipping_threshold_cents' => filled($data['free_shipping_threshold'] ?? null)
                ? (int) round(((float) $data['free_shipping_threshold']) * 100)
                : null,
            'google_analytics_id' => $data['google_analytics_id'] ?? null,
            'theme_colors' => $themeColors,
        ];
    }

    private function syncMaintenanceMode(StoreSetting $settings): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if ($settings->maintenance_enabled) {
            Artisan::call('down', array_filter([
                '--secret' => $settings->maintenance_secret,
                '--render' => 'errors.store-maintenance',
                '--retry' => 60,
            ]));
        } else {
            Artisan::call('up');
        }
    }

    private function applyRuntimeConfig(StoreSetting $settings): void
    {
        config([
            'app.name' => $settings->store_name,
            'app.timezone' => $settings->timezone,
        ]);

        if ($settings->mail_from_name) {
            config(['mail.from.name' => $settings->mail_from_name]);
        }

        if ($settings->mail_from_address) {
            config(['mail.from.address' => $settings->mail_from_address]);
        }
    }

    private function storeFile(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
