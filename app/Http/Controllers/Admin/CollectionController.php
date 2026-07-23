<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CollectionRequest;
use App\Models\Collection;
use App\Services\Admin\CollectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function __construct(private CollectionService $collections) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('collections.view'), 403);

        return view('admin.collections.index', [
            'title' => 'Collections',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Collections']],
            'collections' => $this->collections->list($request->only(['search', 'featured'])),
            'filters' => $request->only(['search', 'featured']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('collections.manage'), 403);

        return view('admin.collections.create', [
            'title' => 'Create Collection',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Collections', 'href' => route('admin.collections.index')], ['label' => 'Create']],
            'products' => $this->collections->productOptions(),
        ]);
    }

    public function store(CollectionRequest $request): RedirectResponse
    {
        $collection = $this->collections->create(
            $request->validated(),
            $request->file('image'),
            $request->file('banner'),
        );

        return redirect()->route('admin.collections.show', $collection)->with('success', 'Collection created successfully.');
    }

    public function show(Collection $collection): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('collections.view'), 403);

        return view('admin.collections.show', [
            'title' => $collection->name,
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Collections', 'href' => route('admin.collections.index')], ['label' => $collection->name]],
            'collection' => $this->collections->find($collection->id),
        ]);
    }

    public function edit(Collection $collection): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('collections.manage'), 403);
        $collection = $this->collections->find($collection->id);

        return view('admin.collections.edit', [
            'title' => 'Edit Collection',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Collections', 'href' => route('admin.collections.index')], ['label' => $collection->name]],
            'collection' => $collection,
            'products' => $this->collections->productOptions(),
        ]);
    }

    public function update(CollectionRequest $request, Collection $collection): RedirectResponse
    {
        $this->collections->update(
            $collection,
            $request->validated(),
            $request->file('image'),
            $request->file('banner'),
        );

        return redirect()->route('admin.collections.show', $collection)->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('collections.manage'), 403);
        $this->collections->delete($collection);

        return redirect()->route('admin.collections.index')->with('success', 'Collection deleted successfully.');
    }
}
