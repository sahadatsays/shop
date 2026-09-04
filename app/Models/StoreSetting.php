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
        'flat_shipping_cents',
        'inside_dhaka_shipping_cents',
        'outside_dhaka_shipping_cents',
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
            'flat_shipping_cents' => 'integer',
            'inside_dhaka_shipping_cents' => 'integer',
            'outside_dhaka_shipping_cents' => 'integer',
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
        $headerText = self::contrastingTextColor($colors['header_bg'], $colors['header_text']);

        return [
            '--store-header-bg' => $colors['header_bg'],
            '--store-header-text' => $headerText,
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

    public static function contrastingTextColor(string $background, string $text): string
    {
        if (self::contrastRatio($background, $text) >= 4.5) {
            return $text;
        }

        return self::relativeLuminance($background) < 0.5 ? '#e2e8f0' : '#0f172a';
    }

    public static function contrastRatio(string $background, string $foreground): float
    {
        $backgroundLuminance = self::relativeLuminance($background);
        $foregroundLuminance = self::relativeLuminance($foreground);
        $lighter = max($backgroundLuminance, $foregroundLuminance);
        $darker = min($backgroundLuminance, $foregroundLuminance);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    public static function relativeLuminance(string $hex): float
    {
        [$red, $green, $blue] = self::hexToRgb($hex);

        $channels = array_map(function (int $channel): float {
            $normalized = $channel / 255;

            return $normalized <= 0.03928
                ? $normalized / 12.92
                : (($normalized + 0.055) / 1.055) ** 2.4;
        }, [$red, $green, $blue]);

        return (0.2126 * $channels[0]) + (0.7152 * $channels[1]) + (0.0722 * $channels[2]);
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
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
            'header_text' => '#e2e8f0',
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
