<?php

namespace App\Models;

use App\Models\Concerns\HasAppNotifications;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasAppNotifications, HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    /**
     * @return HasMany<DashboardUserWidget, $this>
     */
    public function dashboardWidgetPreferences(): HasMany
    {
        return $this->hasMany(DashboardUserWidget::class);
    }

    public function hasPermission(string $slug): bool
    {
        $this->loadMissing('roles.permissions');

        if ($this->hasRole('owner')) {
            return true;
        }

        return $this->permissionSlugs()->contains($slug);
    }

    /**
     * @return Collection<int, string>
     */
    public function permissionSlugs(): Collection
    {
        return $this->roles
            ->flatMap(fn (Role $role): Collection => $role->permissions->pluck('slug'))
            ->unique()
            ->values();
    }

    public function primaryRole(): ?Role
    {
        return $this->roles->first();
    }

    public function displayRoleName(): string
    {
        return $this->primaryRole()?->name ?? 'Admin';
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'AD';
    }
}
