<?php

namespace App\Models;

use App\Enums\RefundReason;
use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'amount_cents',
        'reason',
        'status',
        'restore_stock',
        'payment_reference',
        'notes',
        'processed_by',
        'processed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'reason' => RefundReason::class,
            'status' => RefundStatus::class,
            'restore_stock' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
