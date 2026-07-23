<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\Admin\BrandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(private BrandService $brands) {}

    public function index(Request $request): View
    {
        $brands = $this->brands->list([
            'search' => $request->string('search')->toString() ?: null,
            'status' => $request->string('status')->toString() ?: null,
            'featured' => $request->boolean('featured'),
            'trashed' => $request->boolean('trashed'),
        ]);

        return view('admin.brands.index', [
            'title' => 'Brands',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Brands'],
            ],
            'brands' => $brands,
            'filters' => $request->only(['search', 'status', 'featured', 'trashed']),
        ]);
    }

    public function create(): View
    {
        return view('admin.brands.create', [
            'title' => 'Create Brand',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Brands', 'href' => route('admin.brands.index')],
                ['label' => 'Create'],
            ],
            'form' => $this->brands->formData(),
        ]);
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $this->brands->create(
            $request->validated(),
            $request->file('logo'),
        );

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function show(Brand $brand): View
    {
        $brand->loadCount('products');

        return view('admin.brands.show', [
            'title' => $brand->name,
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Brands', 'href' => route('admin.brands.index')],
                ['label' => $brand->name],
            ],
            'brand' => $brand,
        ]);
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', [
            'title' => 'Edit Brand',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Brands', 'href' => route('admin.brands.index')],
                ['label' => $brand->name, 'href' => route('admin.brands.show', $brand)],
                ['label' => 'Edit'],
            ],
            'form' => $this->brands->formData($brand),
        ]);
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $this->brands->update(
            $brand,
            $request->validated(),
            $request->file('logo'),
        );

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brands->delete($brand);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand moved to trash.');
    }

    public function restore(Brand $brand): RedirectResponse
    {
        $this->brands->restore($brand->id);

        return redirect()
            ->route('admin.brands.index', ['trashed' => 1])
            ->with('success', 'Brand restored successfully.');
    }
}
