<?php

namespace App\Support\Dashboard;

use App\Support\Dashboard\Contracts\WidgetProvider;
use Illuminate\Contracts\Container\Container;

/**
 * Maps widget keys to their provider classes. Providers are resolved lazily
 * from the container so a widget row in the database with no matching provider
 * simply renders as "unavailable" instead of breaking the dashboard.
 */
class WidgetRegistry
{
    /**
     * @var array<string, class-string<WidgetProvider>>
     */
    private array $providers = [];

    public function __construct(private readonly Container $container) {}

    /**
     * @param  class-string<WidgetProvider>  $providerClass
     */
    public function register(string $key, string $providerClass): void
    {
        $this->providers[$key] = $providerClass;
    }

    public function has(string $key): bool
    {
        return isset($this->providers[$key]);
    }

    public function get(string $key): ?WidgetProvider
    {
        if (! $this->has($key)) {
            return null;
        }

        return $this->container->make($this->providers[$key]);
    }

    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->providers);
    }
}
