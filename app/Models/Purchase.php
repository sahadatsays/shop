<?php

namespace App\Models;

use App\Enums\PurchasePaymentStatus;
use App\Enums\PurchaseStatus;
use Database\Factories\PurchaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    /** @use HasFactory<PurchaseFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'purchase_date',
        'expected_delivery_date',
        'status',
        'payment_status',
        'notes',
        'subtotal_cents',
        'discount_cents',
        'shipping_cents',
        'tax_cents',
        'grand_total_cents',
        'paid_cents',
        'created_by',
        'approved_by',
        'submitted_at',
        'approved_at',
        'completed_at',
        'cancelled_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
        'payment_status' => 'unpaid',
        'subtotal_cents' => 0,
        'discount_cents' => 0,
        'shipping_cents' => 0,
        'tax_cents' => 0,
        'grand_total_cents' => 0,
        'paid_cents' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'expected_delivery_date' => 'date',
            'status' => PurchaseStatus::class,
            'payment_status' => PurchasePaymentStatus::class,
            'subtotal_cents' => 'integer',
            'discount_cents' => 'integer',
            'shipping_cents' => 'integer',
            'tax_cents' => 'integer',
            'grand_total_cents' => 'integer',
            'paid_cents' => 'integer',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<PurchaseItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<PurchaseReceipt, $this>
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class)->orderByDesc('received_at');
    }

    public function dueCents(): int
    {
        return max(0, $this->grand_total_cents - $this->paid_cents);
    }

    public function totalQuantityOrdered(): int
    {
        return (int) $this->items->sum('quantity_ordered');
    }

    public function totalQuantityReceived(): int
    {
        return (int) $this->items->sum('quantity_received');
    }

    public function totalQuantityRemaining(): int
    {
        return max(0, $this->totalQuantityOrdered() - $this->totalQuantityReceived());
    }

    public function isFullyReceived(): bool
    {
        if ($this->items->isEmpty()) {
            return false;
        }

        return $this->items->every(fn (PurchaseItem $item): bool => $item->quantityRemaining() === 0);
    }

    public function hasReceivedStock(): bool
    {
        return $this->items->contains(fn (PurchaseItem $item): bool => $item->quantity_received > 0);
    }
}
