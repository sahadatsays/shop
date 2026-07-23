<?php

namespace App\Services\Admin\Dashboard;

use App\DTOs\Admin\Dashboard\ResolvedWidget;
use App\Models\DashboardWidget;
use App\Models\User;
use App\Support\Dashboard\WidgetContext;
use App\Support\Dashboard\WidgetRegistry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Resolves the dynamic set of widgets a given admin may see, merges their saved
 * layout preferences, and renders individual widget payloads (with caching).
 */
class DashboardComposer
{
    public function __construct(private readonly WidgetRegistry $registry) {}

    /**
     * The ordered, permission-filtered widgets for a user, merged with their
     * personal preferences. Data is NOT computed here — bodies are rendered
     * lazily/async through {@see computeData()}.
     *
     * @return Collection<int, ResolvedWidget>
     */
    public function widgetsFor(User $user): Collection
    {
        $preferences = $user->dashboardWidgetPreferences()->get()->keyBy('dashboard_widget_id');

        return DashboardWidget::query()
            ->active()
            ->ordered()
            ->with('roles:id')
            ->get()
            ->filter(fn (DashboardWidget $widget): bool => $this->authorized($widget, $user))
            ->map(function (DashboardWidget $widget) use ($preferences): ResolvedWidget {
                $preference = $preferences->get($widget->id);

                return new ResolvedWidget(
                    widget: $widget,
                    visible: $preference ? (bool) $preference->is_visible : true,
                    collapsed: $preference ? (bool) $preference->is_collapsed : false,
                    pinned: $preference ? (bool) $preference->is_pinned : false,
                    position: $preference && $preference->position !== null ? $preference->position : $widget->display_order,
                    width: $preference && $preference->width !== null ? $preference->width : $widget->width,
                    hasProvider: $this->registry->has($widget->key),
                );
            })
            ->sortBy(fn (ResolvedWidget $resolved): string => ($resolved->pinned ? '0' : '1').str_pad((string) $resolved->position, 6, '0', STR_PAD_LEFT))
            ->values();
    }

    public function resolveWidget(User $user, string $key): ?ResolvedWidget
    {
        return $this->widgetsFor($user)->first(fn (ResolvedWidget $resolved): bool => $resolved->key() === $key);
    }

    /**
     * Compute (and cache) a widget's data payload for the given context.
     *
     * @return array<string, mixed>
     */
    public function computeData(ResolvedWidget $resolved, WidgetContext $context): array
    {
        $provider = $this->registry->get($resolved->key());

        if ($provider === null) {
            return [];
        }

        $ttl = $provider->cacheTtl();

        if ($ttl <= 0) {
            return $provider->data($context);
        }

        $cacheKey = config('dashboard.cache_prefix', 'admin:dashboard').':data:'.$resolved->key().':'.$context->signature();

        return Cache::remember($cacheKey, $ttl, fn (): array => $provider->data($context));
    }

    public function viewFor(string $key): ?string
    {
        return $this->registry->get($key)?->view();
    }

    /**
     * Permission + role gating. A widget with no permission and no assigned
     * roles is visible to any dashboard user. Owners bypass role gates.
     */
    private function authorized(DashboardWidget $widget, User $user): bool
    {
        if ($widget->permission !== null && ! $user->hasPermission($widget->permission)) {
            return false;
        }

        $roleIds = $widget->roles->pluck('id');

        if ($roleIds->isNotEmpty()) {
            if ($user->hasRole('owner')) {
                return true;
            }

            $user->loadMissing('roles:id');

            if ($user->roles->pluck('id')->intersect($roleIds)->isEmpty()) {
                return false;
            }
        }

        return true;
    }
}
