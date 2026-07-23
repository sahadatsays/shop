<?php

namespace App\Services\Admin\Dashboard;

use App\Models\DashboardUserWidget;
use App\Models\DashboardWidget;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Persists per-user dashboard layout: visibility, collapse, pin, order, width.
 */
class WidgetPreferenceService
{
    /**
     * Persist a batch of layout preferences.
     *
     * @param  array<int, array<string, mixed>>  $items  each item keyed by widget "key" plus any of
     *                                                   is_visible|is_collapsed|is_pinned|position|width
     */
    public function saveLayout(User $user, array $items): void
    {
        $widgetsByKey = DashboardWidget::query()
            ->whereIn('key', collect($items)->pluck('key')->filter()->all())
            ->get()
            ->keyBy('key');

        DB::transaction(function () use ($user, $items, $widgetsByKey): void {
            foreach ($items as $index => $item) {
                $widget = $widgetsByKey->get($item['key'] ?? null);

                if ($widget === null) {
                    continue;
                }

                $attributes = $this->sanitize($item, $widget);

                if (! array_key_exists('position', $attributes) && isset($item['position'])) {
                    $attributes['position'] = (int) $item['position'];
                }

                DashboardUserWidget::query()->updateOrCreate(
                    ['user_id' => $user->id, 'dashboard_widget_id' => $widget->id],
                    $attributes,
                );
            }
        });
    }

    public function reset(User $user): void
    {
        $user->dashboardWidgetPreferences()->delete();
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function sanitize(array $item, DashboardWidget $widget): array
    {
        $attributes = [];

        if (array_key_exists('is_visible', $item)) {
            $attributes['is_visible'] = (bool) $item['is_visible'];
        }

        if (array_key_exists('is_collapsed', $item)) {
            $attributes['is_collapsed'] = (bool) $item['is_collapsed'];
        }

        if (array_key_exists('is_pinned', $item)) {
            $attributes['is_pinned'] = (bool) $item['is_pinned'];
        }

        if (array_key_exists('position', $item)) {
            $attributes['position'] = max(0, (int) $item['position']);
        }

        if (array_key_exists('width', $item) && $item['width'] !== null) {
            $attributes['width'] = $this->clampWidth((int) $item['width']);
        }

        return $attributes;
    }

    private function clampWidth(int $width): int
    {
        $min = (int) config('dashboard.min_width', 3);
        $max = (int) config('dashboard.max_width', 12);

        return max($min, min($max, $width));
    }
}
