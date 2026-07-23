<?php

use App\Enums\SubscriptionStatus;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('visitor can subscribe to the newsletter', function (): void {
    $this->postJson(route('newsletter.subscribe'), [
        'email' => 'new.subscriber@example.com',
    ])
        ->assertSuccessful()
        ->assertJsonPath('created', true);

    $this->assertDatabaseHas('newsletter_subscribers', [
        'email' => 'new.subscriber@example.com',
        'status' => SubscriptionStatus::Subscribed->value,
    ]);
});

test('duplicate newsletter emails are not created twice', function (): void {
    NewsletterSubscriber::factory()->create([
        'email' => 'already@example.com',
    ]);

    $this->postJson(route('newsletter.subscribe'), [
        'email' => 'already@example.com',
    ])
        ->assertSuccessful()
        ->assertJsonPath('created', false);

    expect(NewsletterSubscriber::query()->where('email', 'already@example.com')->count())->toBe(1);
});
