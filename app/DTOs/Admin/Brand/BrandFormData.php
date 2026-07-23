<?php

namespace App\DTOs\Admin\Brand;

use App\Models\Brand;

readonly class BrandFormData
{
    public function __construct(
        public ?Brand $brand,
    ) {}
}
