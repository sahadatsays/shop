<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'subtotal_cents',
        'discount_cents',
        'shipping_cents',
        'tax_cents',
        'total_cents',
        'shipping_address',
        'billing_address',
        'courier_name',
        'tracking_number',
        'estimated_delivery_at',
        'delivery_instructions',
        'placed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal_cents' => 'integer',
            'discount_cents' => 'integer',
            'shipping_cents' => 'integer',
            'tax_cents' => 'integer',
            'total_cents' => 'integer',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'estimated_delivery_at' => 'datetime',
            'placed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function getTrackingNumberDisplayAttribute(): ?string
    {
        if (! $this->tracking_number) {
            return null;
        }

        return trim(chunk_split(preg_replace('/\s+/', '', $this->tracking_number) ?? '', 4, ' '));
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<OrderTimelineEvent, $this>
     */
    public function timelineEvents(): HasMany
    {
        return $this->hasMany(OrderTimelineEvent::class);
    }

    /**
     * @return HasMany<OrderTimelineEvent, $this>
     */
    public function statusHistory(): HasMany
    {
        return $this->timelineEvents();
    }

    /**
     * @return HasMany<OrderNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(OrderNote::class)->latest();
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', OrderStatus::pendingStatuses());
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopePlacedToday(Builder $query): Builder
    {
        return $query->whereDate('placed_at', today());
    }
}
