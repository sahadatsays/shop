<?php

namespace App\Models;

use App\Enums\AuditAction;
use App\Enums\AuditCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'action',
        'category',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'ip_address',
        'user_agent',
        'browser',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'category' => AuditCategory::class,
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function causerName(): string
    {
        if ($this->causer instanceof User) {
            return $this->causer->name;
        }

        if ($this->causer instanceof Customer) {
            return $this->causer->name;
        }

        return $this->properties['email'] ?? 'System';
    }

    public function subjectLabel(): ?string
    {
        if ($this->subject instanceof Product) {
            return $this->subject->name;
        }

        if ($this->subject instanceof Order) {
            return $this->subject->order_number;
        }

        if ($this->subject instanceof Customer) {
            return $this->subject->name;
        }

        if ($this->subject instanceof User) {
            return $this->subject->name;
        }

        return $this->properties['subject_label'] ?? null;
    }

    /**
     * @param  Builder<AuditLog>  $query
     * @return Builder<AuditLog>
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if ($category) {
            $query->where('category', $category);
        }

        return $query;
    }
}
