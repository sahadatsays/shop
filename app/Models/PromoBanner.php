<?php

namespace App\Models;

use App\Enums\PromoBannerLayout;
use Database\Factories\PromoBannerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PromoBanner extends Model
{
    /** @use HasFactory<PromoBannerFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'layout',
        'title',
        'image_path',
        'button_label',
        'url',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'layout' => PromoBannerLayout::class,
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * @param  Builder<PromoBanner>  $query
     * @return Builder<PromoBanner>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<PromoBanner>  $query
     * @return Builder<PromoBanner>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
