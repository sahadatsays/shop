<?php

namespace App\Services\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PermissionMatrixService
{
    /**
     * @return array{
     *     roles: Collection<int, Role>,
     *     permissions: Collection<int, Permission>,
     *     groupedPermissions: Collection<string, Collection<int, Permission>>,
     *     matrix: array<int, array<int, bool>>
     * }
     */
    public function data(): array
    {
        $roles = Role::query()->with('permissions')->orderBy('name')->get();
        $permissions = Permission::query()->orderBy('group')->orderBy('name')->get();

        $matrix = [];

        foreach ($roles as $role) {
            $assigned = $role->permissions->pluck('id')->all();

            foreach ($permissions as $permission) {
                $matrix[$role->id][$permission->id] = in_array($permission->id, $assigned, true);
            }
        }

        return [
            'roles' => $roles,
            'permissions' => $permissions,
            'groupedPermissions' => $permissions->groupBy('group'),
            'matrix' => $matrix,
        ];
    }

    /**
     * @param  array<int, array<int>>  $assignments
     */
    public function sync(array $assignments): void
    {
        DB::transaction(function () use ($assignments): void {
            $roles = Role::query()->get()->keyBy('id');

            foreach ($assignments as $roleId => $permissionIds) {
                $role = $roles->get((int) $roleId);

                if (! $role) {
                    continue;
                }

                $role->permissions()->sync(array_values(array_unique(array_map('intval', $permissionIds))));
            }
        });
    }
}
