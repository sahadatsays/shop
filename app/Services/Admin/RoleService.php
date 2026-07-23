<?php

namespace App\Services\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Role::query()->withCount(['permissions', 'users']);

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate(15)->withQueryString();
    }

    public function find(int $id): Role
    {
        return Role::query()
            ->with(['permissions', 'users'])
            ->withCount(['permissions', 'users'])
            ->findOrFail($id);
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data): Role {
            $role = Role::query()->create([
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['slug'] ?? Role::slugFromName($data['name'])),
                'description' => $data['description'] ?? null,
                'is_system' => false,
            ]);

            $role->permissions()->sync($data['permissions'] ?? []);

            return $this->find($role->id);
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data): Role {
            $role->update([
                'name' => $data['name'],
                'slug' => $role->is_system
                    ? $role->slug
                    : $this->uniqueSlug($data['slug'] ?? Role::slugFromName($data['name']), $role->id),
                'description' => $data['description'] ?? null,
            ]);

            if (array_key_exists('permissions', $data)) {
                $role->permissions()->sync($data['permissions']);
            }

            return $this->find($role->id);
        });
    }

    public function delete(Role $role): void
    {
        if ($role->is_system) {
            abort(422, 'System roles cannot be deleted.');
        }

        if ($role->users()->exists()) {
            abort(422, 'Remove users from this role before deleting it.');
        }

        $role->delete();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function permissionOptions(): Collection
    {
        return Permission::query()->orderBy('group')->orderBy('name')->get();
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug, '_') ?: 'role';
        $candidate = $base;
        $suffix = 1;

        while (
            Role::query()
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
