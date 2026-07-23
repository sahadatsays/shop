<?php

namespace App\DTOs\Admin\Dashboard;

readonly class FeaturedBrandData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $logoUrl,
        public int $productCount,
    ) {}
}
