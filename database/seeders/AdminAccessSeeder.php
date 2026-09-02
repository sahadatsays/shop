<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminAccessSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->seedPermissions();
        $roles = $this->seedRoles($permissions);
        $this->seedUsers($roles);
    }

    /**
     * @return array<string, Permission>
     */
    private function seedPermissions(): array
    {
        $records = [];

        foreach (config('admin-permissions.groups', []) as $group => $items) {
            foreach ($items as $slug => $name) {
                $records[$slug] = Permission::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'group' => $group,
                        'description' => null,
                        'is_system' => true,
                    ],
                );
            }
        }

        return $records;
    }

    /**
     * @param  array<string, Permission>  $permissions
     * @return array<string, Role>
     */
    private function seedRoles(array $permissions): array
    {
        $roles = [];

        foreach (config('admin-permissions.roles', []) as $slug => $attributes) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $attributes['name'],
                    'description' => $attributes['description'] ?? null,
                    'is_system' => true,
                ],
            );

            $assigned = config("admin-permissions.role_permissions.{$slug}", []);

            if ($assigned === '*') {
                $role->permissions()->sync(collect($permissions)->pluck('id'));
            } else {
                $role->permissions()->sync(
                    collect($assigned)
                        ->map(fn (string $permissionSlug): ?int => $permissions[$permissionSlug]->id ?? null)
                        ->filter()
                        ->values()
                        ->all(),
                );
            }

            $roles[$slug] = $role->fresh('permissions');
        }

        return $roles;
    }

    /**
     * @param  array<string, Role>  $roles
     */
    private function seedUsers(array $roles): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        foreach (config('admin-permissions.default_users', []) as $userData) {
            $user = User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => 'password',
                    'is_active' => true,
                ],
            );

            $role = $roles[$userData['role']] ?? null;

            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }
    }
}
