<?php

namespace Database\Seeders;

use App\Models\DashboardWidget;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DashboardWidgetSeeder extends Seeder
{
    public function run(): void
    {
        $roleIdsBySlug = Role::query()->pluck('id', 'slug');

        foreach (config('dashboard.widgets', []) as $definition) {
            $roles = $definition['roles'] ?? [];
            unset($definition['roles']);

            $key = $definition['key'];
            $definition['is_system'] = true;

            // Only create missing widgets — never overwrite admin edits.
            if (DashboardWidget::query()->where('key', $key)->exists()) {
                continue;
            }

            $widget = DashboardWidget::query()->create($definition);

            $roleIds = collect($roles)
                ->map(fn (string $slug): ?int => $roleIdsBySlug->get($slug))
                ->filter()
                ->values()
                ->all();

            $widget->roles()->sync($roleIds);
        }
    }
}
