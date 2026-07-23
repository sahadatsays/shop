<?php

namespace App\DTOs\Admin\Category;

use App\Models\Category;
use Illuminate\Support\Collection;

readonly class CategoryFormData
{
    /**
     * @param  Collection<int, Category>  $parentOptions
     */
    public function __construct(
        public ?Category $category,
        public Collection $parentOptions,
    ) {}
}
