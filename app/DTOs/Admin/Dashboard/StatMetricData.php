<?php

namespace App\DTOs\Admin\Dashboard;

readonly class StatMetricData
{
    public function __construct(
        public string $label,
        public string $value,
        public ?string $change = null,
        public string $trend = 'neutral',
        public ?string $icon = null,
    ) {}
}
