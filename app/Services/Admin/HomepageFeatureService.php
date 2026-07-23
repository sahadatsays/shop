<?php

namespace App\Services\Admin;

use App\Models\HomepageFeature;
use App\Support\HomepageSettings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HomepageFeatureService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = HomepageFeature::query();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->ordered()->paginate(15)->withQueryString();
    }

    public function find(int $id): HomepageFeature
    {
        return HomepageFeature::query()->findOrFail($id);
    }

    public function create(array $data): HomepageFeature
    {
        $feature = HomepageFeature::query()->create($this->prepareAttributes($data));
        HomepageSettings::clearCache();

        return $feature;
    }

    public function update(HomepageFeature $feature, array $data): HomepageFeature
    {
        $feature->update($this->prepareAttributes($data));
        HomepageSettings::clearCache();

        return $feature->refresh();
    }

    public function delete(HomepageFeature $feature): void
    {
        $feature->delete();
        HomepageSettings::clearCache();
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $data): array
    {
        return [
            'icon' => $data['icon'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }
}
