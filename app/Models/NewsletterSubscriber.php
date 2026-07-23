<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Database\Factories\NewsletterSubscriberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    /** @use HasFactory<NewsletterSubscriberFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'status',
        'subscribed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'subscribed_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<NewsletterSubscriber>  $query
     * @return Builder<NewsletterSubscriber>
     */
    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::Subscribed);
    }
}
