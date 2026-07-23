<?php

namespace App\DTOs\Admin\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;

readonly class ProductFormData
{
    /**
     * @param  Collection<int, Category>  $categoryOptions
     * @param  Collection<int, Brand>  $brandOptions
     * @param  Collection<int, Product>  $relatedProductOptions
     */
    public function __construct(
        public ?Product $product,
        public Collection $categoryOptions,
        public Collection $brandOptions,
        public Collection $relatedProductOptions,
    ) {}
}
