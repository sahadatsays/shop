<?php

namespace App\Support\Dashboard\Contracts;

use App\Support\Dashboard\WidgetContext;

/**
 * Every dashboard widget is backed by a provider that computes its data and
 * declares which Blade partial renders it. Providers are stateless and are
 * resolved from the container, so they may inject any services they need.
 */
interface WidgetProvider
{
    /**
     * Compute the widget payload for the given context.
     *
     * @return array<string, mixed>
     */
    public function data(WidgetContext $context): array;

    /**
     * The Blade view rendering this widget's body.
     */
    public function view(): string;

    /**
     * Cache TTL (seconds) for the computed payload. 0 disables caching.
     */
    public function cacheTtl(): int;
}
