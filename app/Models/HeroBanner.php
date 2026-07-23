<?php

namespace App\Models;

use Database\Factories\HeroBannerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroBanner extends Model
{
    /** @use HasFactory<HeroBannerFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'badge_text',
        'desktop_image_path',
        'mobile_image_path',
        'primary_label',
        'primary_url',
        'secondary_label',
        'secondary_url',
        'starts_at',
        'ends_at',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function desktopImageUrl(): ?string
    {
        if (! $this->desktop_image_path) {
            return null;
        }

        if (str_starts_with($this->desktop_image_path, 'http')) {
            return $this->desktop_image_path;
        }

        return Storage::disk('public')->url($this->desktop_image_path);
    }

    public function mobileImageUrl(): ?string
    {
        if (! $this->mobile_image_path) {
            return $this->desktopImageUrl();
        }

        if (str_starts_with($this->mobile_image_path, 'http')) {
            return $this->mobile_image_path;
        }

        return Storage::disk('public')->url($this->mobile_image_path);
    }

    /**
     * @param  Builder<HeroBanner>  $query
     * @return Builder<HeroBanner>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<HeroBanner>  $query
     * @return Builder<HeroBanner>
     */
    public function scopeActiveNow(Builder $query): Builder
    {
        $now = now();

        return $query->active()
            ->where(fn (Builder $builder) => $builder->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn (Builder $builder) => $builder->whereNull('ends_at')->orWhere('ends_at', '>', $now));
    }

    /**
     * @param  Builder<HeroBanner>  $query
     * @return Builder<HeroBanner>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
