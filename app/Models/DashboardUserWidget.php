<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardUserWidget extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'dashboard_widget_id',
        'is_visible',
        'is_collapsed',
        'is_pinned',
        'position',
        'width',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_collapsed' => 'boolean',
            'is_pinned' => 'boolean',
            'position' => 'integer',
            'width' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<DashboardWidget, $this>
     */
    public function widget(): BelongsTo
    {
        return $this->belongsTo(DashboardWidget::class, 'dashboard_widget_id');
    }
}
