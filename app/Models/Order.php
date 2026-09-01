<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
        'refunded_cents',
        'shipping_address',
        'billing_address',
        'courier_name',
        'tracking_number',
        'estimated_delivery_at',
        'delivery_instructions',
        'placed_at',
        'return_requested_at',
        'return_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'subtotal_cents' => 'integer',
            'discount_cents' => 'integer',
            'shipping_cents' => 'integer',
            'tax_cents' => 'integer',
            'total_cents' => 'integer',
            'refunded_cents' => 'integer',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'estimated_delivery_at' => 'datetime',
            'placed_at' => 'datetime',
            'return_requested_at' => 'datetime',
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
     * @return HasMany<Refund, $this>
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class)->latest();
    }

    public function refundableCents(): int
    {
        return max(0, $this->total_cents - $this->refunded_cents);
    }

    public function isFullyRefunded(): bool
    {
        return $this->refunded_cents >= $this->total_cents
            || $this->payment_status === PaymentStatus::Refunded;
    }

    public function canRequestReturn(): bool
    {
        if ($this->status !== OrderStatus::Delivered) {
            return false;
        }

        if ($this->return_requested_at !== null) {
            return false;
        }

        if (in_array($this->status, [OrderStatus::Returned, OrderStatus::Refunded, OrderStatus::Cancelled], true)) {
            return false;
        }

        $deliveredAt = $this->deliveredAt();

        if ($deliveredAt === null) {
            return false;
        }

        return $deliveredAt->gte(now()->subDays(config('refunds.return_window_days', 30)));
    }

    public function deliveredAt(): ?Carbon
    {
        $event = $this->relationLoaded('timelineEvents')
            ? $this->timelineEvents->where('status', OrderStatus::Delivered)->sortByDesc('created_at')->first()
            : $this->timelineEvents()->where('status', OrderStatus::Delivered)->latest()->first();

        return $event?->created_at ?? $this->estimated_delivery_at;
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeRefundable(Builder $query): Builder
    {
        return $query->whereIn('payment_status', [
            PaymentStatus::Paid->value,
            PaymentStatus::PartiallyRefunded->value,
        ]);
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeNeedsRefundAttention(Builder $query): Builder
    {
        return $query->where(function ($builder): void {
            $builder->where('status', OrderStatus::Returned)
                ->orWhereNotNull('return_requested_at');
        })->whereIn('payment_status', [
            PaymentStatus::Paid->value,
            PaymentStatus::PartiallyRefunded->value,
        ]);
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
