<?php

namespace App\Services\Admin;

use App\Enums\SubscriptionStatus;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsletterSubscriberService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = NewsletterSubscriber::query()->latest('subscribed_at');

        if ($search = $filters['search'] ?? null) {
            $query->where('email', 'like', "%{$search}%");
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', SubscriptionStatus::from($status));
        }

        return $query->paginate(20)->withQueryString();
    }
}
