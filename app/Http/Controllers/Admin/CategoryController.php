<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categories) {}

    public function index(Request $request): View
    {
        $categories = $this->categories->list([
            'search' => $request->string('search')->toString() ?: null,
            'status' => $request->string('status')->toString() ?: null,
            'trashed' => $request->boolean('trashed'),
        ]);

        return view('admin.categories.index', [
            'title' => 'Categories',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Categories'],
            ],
            'categories' => $categories,
            'filters' => $request->only(['search', 'status', 'trashed']),
        ]);
    }

    public function create(): View
    {
        $form = $this->categories->formData();

        return view('admin.categories.create', [
            'title' => 'Create Category',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Categories', 'href' => route('admin.categories.index')],
                ['label' => 'Create'],
            ],
            'form' => $form,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categories->create(
            $request->validated(),
            $request->file('image'),
            $request->file('banner'),
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $category = $this->categories->formData($category)->category;

        return view('admin.categories.edit', [
            'title' => 'Edit Category',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Categories', 'href' => route('admin.categories.index')],
                ['label' => $category->name],
            ],
            'form' => $this->categories->formData($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categories->update(
            $category,
            $request->validated(),
            $request->file('image'),
            $request->file('banner'),
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category moved to trash.');
    }

    public function restore(Category $category): RedirectResponse
    {
        $this->categories->restore($category->id);

        return redirect()
            ->route('admin.categories.index', ['trashed' => 1])
            ->with('success', 'Category restored successfully.');
    }
}
