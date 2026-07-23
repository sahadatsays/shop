<?php

namespace App\Models;

use App\Enums\NotificationAudience;
use App\Enums\NotificationCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AppNotification extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'audience',
        'category',
        'title',
        'body',
        'action_label',
        'action_url',
        'meta',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'audience' => NotificationAudience::class,
            'category' => NotificationCategory::class,
            'meta' => 'array',
            'read_at' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if ($this->isRead()) {
            return;
        }

        $this->forceFill(['read_at' => now()])->save();
    }

    /**
     * @param  Builder<AppNotification>  $query
     * @return Builder<AppNotification>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * @param  Builder<AppNotification>  $query
     * @return Builder<AppNotification>
     */
    public function scopeForNotifiable(Builder $query, Model $notifiable): Builder
    {
        return $query
            ->where('notifiable_type', $notifiable->getMorphClass())
            ->where('notifiable_id', $notifiable->getKey());
    }

    public function timeAgo(): string
    {
        return $this->created_at?->diffForHumans(short: true) ?? '';
    }

    public function groupLabel(): string
    {
        if (! $this->created_at) {
            return 'Earlier';
        }

        if ($this->created_at->isToday()) {
            return 'Today';
        }

        if ($this->created_at->isYesterday()) {
            return 'Yesterday';
        }

        if ($this->created_at->greaterThanOrEqualTo(now()->startOfWeek())) {
            return 'Earlier this week';
        }

        return $this->created_at->format('M j, Y');
    }
}
