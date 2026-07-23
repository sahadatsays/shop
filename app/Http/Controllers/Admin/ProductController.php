<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\Admin\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private ProductService $products) {}

    public function index(Request $request): View
    {
        $products = $this->products->list([
            'search' => $request->string('search')->toString() ?: null,
            'status' => $request->string('status')->toString() ?: null,
            'category_id' => $request->integer('category_id') ?: null,
            'brand_id' => $request->integer('brand_id') ?: null,
            'featured' => $request->boolean('featured'),
            'new_arrival' => $request->boolean('new_arrival'),
            'trashed' => $request->boolean('trashed'),
        ]);

        return view('admin.products.index', [
            'title' => 'Products',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Products'],
            ],
            'products' => $products,
            'filters' => $request->only(['search', 'status', 'category_id', 'brand_id', 'featured', 'new_arrival', 'trashed']),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'title' => 'Create Product',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Products', 'href' => route('admin.products.index')],
                ['label' => 'Create'],
            ],
            'form' => $this->products->formData(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->products->create(
            $request->validated(),
            $request->file('gallery', []),
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product = $this->products->formData($product)->product;

        return view('admin.products.show', [
            'title' => $product->name,
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Products', 'href' => route('admin.products.index')],
                ['label' => $product->name],
            ],
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'title' => 'Edit Product',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Products', 'href' => route('admin.products.index')],
                ['label' => $product->name, 'href' => route('admin.products.show', $product)],
                ['label' => 'Edit'],
            ],
            'form' => $this->products->formData($product),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->products->update(
            $product,
            $request->validated(),
            $request->file('gallery', []),
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product moved to trash.');
    }

    public function restore(Product $product): RedirectResponse
    {
        $this->products->restore($product->id);

        return redirect()
            ->route('admin.products.index', ['trashed' => 1])
            ->with('success', 'Product restored successfully.');
    }
}
