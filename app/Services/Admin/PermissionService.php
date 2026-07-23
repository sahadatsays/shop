<?php

namespace App\Services\Admin;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Permission::query()->withCount('roles');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");
            });
        }

        if ($group = $filters['group'] ?? null) {
            $query->where('group', $group);
        }

        return $query->orderBy('group')->orderBy('name')->paginate(20)->withQueryString();
    }

    public function find(int $id): Permission
    {
        return Permission::query()
            ->with('roles')
            ->withCount('roles')
            ->findOrFail($id);
    }

    public function create(array $data): Permission
    {
        return Permission::query()->create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['slug'] ?? Permission::slugFromName($data['name'])),
            'group' => $data['group'],
            'description' => $data['description'] ?? null,
            'is_system' => false,
        ]);
    }

    public function update(Permission $permission, array $data): Permission
    {
        return DB::transaction(function () use ($permission, $data): Permission {
            $permission->update([
                'name' => $data['name'],
                'slug' => $permission->is_system
                    ? $permission->slug
                    : $this->uniqueSlug($data['slug'] ?? Permission::slugFromName($data['name']), $permission->id),
                'group' => $data['group'],
                'description' => $data['description'] ?? null,
            ]);

            return $this->find($permission->id);
        });
    }

    public function delete(Permission $permission): void
    {
        if ($permission->is_system) {
            abort(422, 'System permissions cannot be deleted.');
        }

        $permission->delete();
    }

    /**
     * @return Collection<int, string>
     */
    public function groups(): Collection
    {
        return Permission::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug, '_') ?: 'permission';
        $candidate = $base;
        $suffix = 1;

        while (
            Permission::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = $base.'_'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
