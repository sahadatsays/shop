<?php

namespace App\Models;

use App\Enums\Admin\DashboardWidgetType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardWidget extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'type',
        'category',
        'width',
        'height',
        'display_order',
        'refresh_interval',
        'permission',
        'is_active',
        'is_system',
        'config',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DashboardWidgetType::class,
            'width' => 'integer',
            'height' => 'integer',
            'display_order' => 'integer',
            'refresh_interval' => 'integer',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'config' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    /**
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return HasMany<DashboardUserWidget, $this>
     */
    public function userWidgets(): HasMany
    {
        return $this->hasMany(DashboardUserWidget::class);
    }

    /**
     * @param  Builder<DashboardWidget>  $query
     * @return Builder<DashboardWidget>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<DashboardWidget>  $query
     * @return Builder<DashboardWidget>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
