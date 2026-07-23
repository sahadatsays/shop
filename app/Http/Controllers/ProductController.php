<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(?Product $product = null): View
    {
        $product ??= Product::query()->published()->ordered()->firstOrFail();

        abort_unless($product->status === ProductStatus::Published, 404);

        $product->load(['images', 'category', 'brand', 'specifications', 'attributes']);

        return view('products.show', [
            'product' => $product,
        ]);
    }
}
