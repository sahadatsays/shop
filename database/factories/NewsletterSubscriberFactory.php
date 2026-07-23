<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsletterSubscriber>
 */
class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'status' => SubscriptionStatus::Subscribed,
            'subscribed_at' => now(),
        ];
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (): array => [
            'status' => SubscriptionStatus::Unsubscribed,
        ]);
    }
}
