<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Str;

class NewsletterService
{
    /**
     * @return array{subscriber: NewsletterSubscriber, created: bool}
     */
    public function subscribe(string $email): array
    {
        $normalized = Str::lower(trim($email));

        $existing = NewsletterSubscriber::query()->where('email', $normalized)->first();

        if ($existing) {
            if ($existing->status === SubscriptionStatus::Unsubscribed) {
                $existing->update([
                    'status' => SubscriptionStatus::Subscribed,
                    'subscribed_at' => now(),
                ]);
            }

            return [
                'subscriber' => $existing->fresh(),
                'created' => false,
            ];
        }

        $subscriber = NewsletterSubscriber::query()->create([
            'email' => $normalized,
            'status' => SubscriptionStatus::Subscribed,
            'subscribed_at' => now(),
        ]);

        return [
            'subscriber' => $subscriber,
            'created' => true,
        ];
    }
}
