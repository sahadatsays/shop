<?php

namespace App\Models\Concerns;

use App\Models\AppNotification;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAppNotifications
{
    /**
     * @return MorphMany<AppNotification, $this>
     */
    public function appNotifications(): MorphMany
    {
        return $this->morphMany(AppNotification::class, 'notifiable')->latest();
    }
}
