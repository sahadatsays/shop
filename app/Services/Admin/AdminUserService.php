<?php

namespace App\Services\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminUserService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = User::query()
            ->with(['roles'])
            ->withCount('roles');

        if ($search = $filters['search'] ?? null) {
            $term = '%'.$search.'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if (($isActive = $filters['is_active'] ?? null) !== null && $isActive !== '') {
            $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
        }

        if ($roleId = $filters['role_id'] ?? null) {
            $query->whereHas('roles', fn ($builder) => $builder->where('roles.id', $roleId));
        }

        return $query->orderBy('name')->paginate(15)->withQueryString();
    }

    public function find(int $id): User
    {
        return User::query()
            ->with(['roles.permissions'])
            ->findOrFail($id);
    }

    /**
     * @return Collection<int, Role>
     */
    public function roleOptions(?User $actor = null): Collection
    {
        $roles = Role::query()->orderBy('name')->get();

        if ($actor !== null && ! $actor->hasRole('owner')) {
            return $roles->reject(fn (Role $role): bool => $role->slug === 'owner')->values();
        }

        return $roles;
    }

    public function create(array $data, User $actor): User
    {
        return DB::transaction(function () use ($data, $actor): User {
            $roleIds = $this->validatedRoleIds($data['roles'] ?? [], $actor);

            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'] ?? true,
            ]);

            $user->roles()->sync($roleIds);

            return $this->find($user->id);
        });
    }

    public function update(User $user, array $data, User $actor): User
    {
        return DB::transaction(function () use ($user, $data, $actor): User {
            $roleIds = $this->validatedRoleIds($data['roles'] ?? [], $actor, $user);

            if ($user->id === $actor->id && ! ($data['is_active'] ?? true)) {
                throw ValidationException::withMessages([
                    'is_active' => 'You cannot deactivate your own account.',
                ]);
            }

            $this->ensureOwnerRemains($user, $roleIds, (bool) ($data['is_active'] ?? $user->is_active));

            $attributes = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? true,
            ];

            if (! empty($data['password'])) {
                $attributes['password'] = Hash::make($data['password']);
            }

            $user->update($attributes);
            $user->roles()->sync($roleIds);

            return $this->find($user->id);
        });
    }

    public function delete(User $user, User $actor): void
    {
        if ($user->id === $actor->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        if ($user->hasRole('owner') && $this->activeOwnerCount(excluding: $user->id) < 1) {
            throw ValidationException::withMessages([
                'user' => 'At least one active owner must remain.',
            ]);
        }

        $user->delete();
    }

    /**
     * @param  array<int, int|string>  $roleIds
     * @return array<int, int>
     */
    private function validatedRoleIds(array $roleIds, User $actor, ?User $target = null): array
    {
        $roleIds = collect($roleIds)->map(fn ($id): int => (int) $id)->unique()->values()->all();

        if ($roleIds === []) {
            throw ValidationException::withMessages([
                'roles' => 'Select at least one role.',
            ]);
        }

        $roles = Role::query()->whereIn('id', $roleIds)->get();

        if ($roles->count() !== count($roleIds)) {
            throw ValidationException::withMessages([
                'roles' => 'One or more selected roles are invalid.',
            ]);
        }

        if (! $actor->hasRole('owner') && $roles->contains('slug', 'owner')) {
            throw ValidationException::withMessages([
                'roles' => 'Only owners can assign the owner role.',
            ]);
        }

        if ($target !== null) {
            $this->ensureOwnerRemains($target, $roleIds, (bool) $target->is_active);
        }

        return $roleIds;
    }

    /**
     * @param  array<int, int>  $roleIds
     */
    private function ensureOwnerRemains(User $user, array $roleIds, bool $willBeActive): void
    {
        if (! $user->hasRole('owner')) {
            return;
        }

        $ownerRoleId = Role::query()->where('slug', 'owner')->value('id');

        if ($ownerRoleId === null) {
            return;
        }

        $willHaveOwnerRole = in_array((int) $ownerRoleId, $roleIds, true);

        if ($willHaveOwnerRole && $willBeActive) {
            return;
        }

        if ($this->activeOwnerCount(excluding: $user->id) < 1) {
            throw ValidationException::withMessages([
                'roles' => 'At least one active owner must remain.',
            ]);
        }
    }

    private function activeOwnerCount(?int $excluding = null): int
    {
        $query = User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($builder) => $builder->where('slug', 'owner'));

        if ($excluding !== null) {
            $query->whereKeyNot($excluding);
        }

        return $query->count();
    }
}
