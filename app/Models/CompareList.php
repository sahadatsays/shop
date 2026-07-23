<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompareList extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'session_id',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<CompareItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CompareItem::class);
    }

    public function itemCount(): int
    {
        return $this->items()->count();
    }

    public function isGuest(): bool
    {
        return $this->customer_id === null;
    }
}
