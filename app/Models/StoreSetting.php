<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoreSetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'store_name',
        'tagline',
        'description',
        'logo_path',
        'favicon_path',
        'email',
        'phone',
        'address',
        'support_email',
        'social_links',
        'currency',
        'timezone',
        'mail_from_name',
        'mail_from_address',
        'maintenance_enabled',
        'maintenance_message',
        'maintenance_secret',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image_path',
        'utility_bar_message',
        'free_shipping_threshold_cents',
        'google_analytics_id',
        'theme_colors',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'theme_colors' => 'array',
            'maintenance_enabled' => 'boolean',
            'free_shipping_threshold_cents' => 'integer',
        ];
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function faviconUrl(): ?string
    {
        return $this->favicon_path ? Storage::disk('public')->url($this->favicon_path) : null;
    }

    public function ogImageUrl(): ?string
    {
        return $this->og_image_path ? Storage::disk('public')->url($this->og_image_path) : null;
    }

    /**
     * @return array<string, string|null>
     */
    public function socialLinks(): array
    {
        return array_merge([
            'instagram' => null,
            'facebook' => null,
            'youtube' => null,
            'x' => null,
        ], $this->social_links ?? []);
    }

    /**
     * @return array<string, string>
     */
    public function themeColors(): array
    {
        return array_merge(self::defaultThemeColors(), $this->theme_colors ?? []);
    }

    public function hasThemeOverrides(): bool
    {
        return filled($this->theme_colors);
    }

    /**
     * @return array<string, string>
     */
    public function themeCssVariables(): array
    {
        $colors = $this->themeColors();

        return [
            '--store-header-bg' => $colors['header_bg'],
            '--store-header-text' => $colors['header_text'],
            '--store-utility-bg' => $colors['utility_bar_bg'],
            '--store-utility-text' => $colors['utility_bar_text'],
            '--store-button-primary-bg' => $colors['button_primary_bg'],
            '--store-button-primary-text' => $colors['button_primary_text'],
            '--store-button-accent-bg' => $colors['button_accent_bg'],
            '--store-button-accent-text' => $colors['button_accent_text'],
            '--store-footer-bg' => $colors['footer_bg'],
            '--store-link-accent' => $colors['link_accent'],
        ];
    }

    public function displayName(): string
    {
        return $this->store_name;
    }

    public function defaultMetaDescription(): string
    {
        return $this->meta_description
            ?? $this->description
            ?? 'Premium gear and goods crafted with the honor, discipline, and quality of those who served.';
    }

    public function contactEmail(): string
    {
        return $this->support_email ?? $this->email ?? 'support@valorsupply.co';
    }

    /**
     * @return array<string, string>
     */
    public static function defaultThemeColors(): array
    {
        return [
            'header_bg' => '#090f1d',
            'header_text' => '#0f172a',
            'utility_bar_bg' => '#090f1d',
            'utility_bar_text' => '#cbd5e4',
            'button_primary_bg' => '#0f172a',
            'button_primary_text' => '#ffffff',
            'button_accent_bg' => '#b08968',
            'button_accent_text' => '#ffffff',
            'footer_bg' => '#090f1d',
            'link_accent' => '#c29e7c',
        ];
    }
}
