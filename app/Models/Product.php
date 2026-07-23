<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Support\HomepageSettings;
use App\Support\MoneyFormatter;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'short_description',
        'description',
        'price_cents',
        'compare_at_price_cents',
        'stock_quantity',
        'low_stock_threshold',
        'status',
        'is_featured',
        'is_new_arrival',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'compare_at_price_cents' => 'integer',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'status' => ProductStatus::class,
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return BelongsToMany<Offer, $this>
     */
    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class)
            ->withPivot(['sale_price_cents', 'sort_order']);
    }

    /**
     * @return BelongsToMany<Collection, $this>
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Collection::class)
            ->withPivot('sort_order');
    }

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->approved();
    }

    public function displayRating(): float
    {
        $average = $this->approvedReviews()->avg('rating');

        return $average !== null
            ? round((float) $average, 1)
            : $this->placeholderRating();
    }

    public function displayReviewCount(): int
    {
        $count = $this->approvedReviews()->count();

        return $count > 0 ? $count : $this->placeholderReviewCount();
    }

    /**
     * @return HasMany<ProductSpecification, $this>
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProductAttribute, $this>
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class)->orderBy('sort_order');
    }

    /**
     * @return BelongsToMany<Product, $this>
     */
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_related',
            'product_id',
            'related_product_id',
        )->withPivot('sort_order')->orderByPivot('sort_order');
    }

    /**
     * @return HasMany<WarehouseStock, $this>
     */
    public function warehouseStock(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity === 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function stockStatusLabel(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of stock';
        }

        if ($this->isLowStock()) {
            return 'Low stock';
        }

        return 'In stock';
    }

    public function stockStatusBadgeVariant(): string
    {
        if ($this->isOutOfStock()) {
            return 'danger';
        }

        if ($this->isLowStock()) {
            return 'warning';
        }

        return 'success';
    }

    public function primaryImageUrl(): ?string
    {
        $image = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $image?->url();
    }

    public function formattedPrice(): string
    {
        return MoneyFormatter::format($this->price_cents);
    }

    public function formattedCompareAtPrice(): ?string
    {
        return $this->compare_at_price_cents !== null
            ? MoneyFormatter::format($this->compare_at_price_cents)
            : null;
    }

    public function isOnSale(): bool
    {
        return $this->compare_at_price_cents !== null
            && $this->compare_at_price_cents > $this->price_cents;
    }

    public function discountPercent(): ?int
    {
        if (! $this->isOnSale()) {
            return null;
        }

        return (int) round((1 - ($this->price_cents / $this->compare_at_price_cents)) * 100);
    }

    public function savingsCents(): ?int
    {
        if (! $this->isOnSale()) {
            return null;
        }

        return $this->compare_at_price_cents - $this->price_cents;
    }

    public function formattedSavings(): ?string
    {
        $savings = $this->savingsCents();

        return $savings !== null ? MoneyFormatter::format($savings) : null;
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function attributesNamed(string ...$names): Collection
    {
        $normalized = collect($names)->map(fn (string $name): string => strtolower($name));

        $attributes = $this->relationLoaded('attributes')
            ? $this->getRelation('attributes')
            : $this->attributes()->get();

        return $attributes->filter(
            fn (ProductAttribute $attribute): bool => $normalized->contains(strtolower($attribute->name)),
        )->values();
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function colorAttributes(): Collection
    {
        return $this->attributesNamed(...config('product.attribute_groups.colors', ['Color']));
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function sizeAttributes(): Collection
    {
        return $this->attributesNamed(...config('product.attribute_groups.sizes', ['Size']));
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function materialAttributes(): Collection
    {
        return $this->attributesNamed(...config('product.attribute_groups.materials', ['Material', 'Materials']));
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function careAttributes(): Collection
    {
        return $this->attributesNamed(...config('product.attribute_groups.care', ['Care', 'Care Instructions']));
    }

    public function colorSwatchClass(string $colorName): string
    {
        /** @var array<string, string> $swatches */
        $swatches = config('product.color_swatches', []);

        return $swatches[$colorName] ?? config('product.default_color_swatch', 'bg-gray-400');
    }

    public function detailStockLabel(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of stock';
        }

        if ($this->isLowStock()) {
            return 'In stock — only '.$this->stock_quantity.' left';
        }

        return 'In stock';
    }

    /**
     * @return array{badge: string|null, variant: string}
     */
    public function shopBadge(): array
    {
        if ($this->isOnSale()) {
            return ['badge' => '-'.$this->discountPercent().'%', 'variant' => 'danger'];
        }

        if ($this->isNew() || $this->is_new_arrival) {
            return ['badge' => 'New', 'variant' => 'olive'];
        }

        if ($this->is_featured) {
            return ['badge' => 'Best seller', 'variant' => 'bronze'];
        }

        if ($this->isLowStock()) {
            return ['badge' => 'Limited', 'variant' => 'navy'];
        }

        return ['badge' => null, 'variant' => 'bronze'];
    }

    public function isNew(?int $days = null): bool
    {
        $days ??= (int) (HomepageSettings::current()->new_badge_days ?: 30);

        return $this->created_at !== null && $this->created_at->gte(now()->subDays($days));
    }

    public function shopStockLabel(): string
    {
        if ($this->isLowStock()) {
            return 'Only '.$this->stock_quantity.' left — order soon';
        }

        return 'In stock';
    }

    public function shopStockPercent(): ?int
    {
        if (! $this->isLowStock()) {
            return null;
        }

        $capacity = max($this->low_stock_threshold * 3, 1);

        return (int) min(100, max(8, round(($this->stock_quantity / $capacity) * 100)));
    }

    public function placeholderRating(): float
    {
        $seed = crc32((string) $this->id);

        return round(4.3 + ($seed % 8) / 10, 1);
    }

    public function placeholderReviewCount(): int
    {
        $min = (int) config('shop.review_count_min', 12);
        $max = (int) config('shop.review_count_max', 150);

        return $min + (crc32($this->slug) % max(1, $max - $min + 1));
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeVisibleOnWebsite(Builder $query): Builder
    {
        return $query->published();
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeShopVisible(Builder $query): Builder
    {
        return $query->visibleOnWebsite();
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeOnSale(Builder $query): Builder
    {
        return $query->whereNotNull('compare_at_price_cents')
            ->whereColumn('compare_at_price_cents', '>', 'price_cents');
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Published);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->published();
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeNewArrival(Builder $query): Builder
    {
        return $query->where('is_new_arrival', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', 0);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
