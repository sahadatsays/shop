<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWarehouseRequest;
use App\Http\Requests\Admin\UpdateWarehouseRequest;
use App\Models\Warehouse;
use App\Services\Admin\WarehouseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function __construct(private WarehouseService $warehouses) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('warehouses.view'), 403);

        return view('admin.warehouses.index', [
            'title' => 'Warehouses',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Warehouses'],
            ],
            'warehouses' => $this->warehouses->list([
                'search' => $request->string('search')->toString() ?: null,
                'is_active' => $request->has('is_active') && $request->input('is_active') !== ''
                    ? $request->string('is_active')->toString()
                    : null,
            ]),
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('warehouses.manage'), 403);

        return view('admin.warehouses.create', [
            'title' => 'Create warehouse',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Warehouses', 'href' => route('admin.warehouses.index')],
                ['label' => 'Create'],
            ],
        ]);
    }

    public function store(StoreWarehouseRequest $request): RedirectResponse
    {
        $warehouse = $this->warehouses->create($request->validated());

        return redirect()
            ->route('admin.warehouses.show', $warehouse)
            ->with('success', 'Warehouse created successfully.');
    }

    public function show(Warehouse $warehouse): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('warehouses.view'), 403);

        $warehouse = $this->warehouses->find($warehouse->id);

        return view('admin.warehouses.show', [
            'title' => $warehouse->name,
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Warehouses', 'href' => route('admin.warehouses.index')],
                ['label' => $warehouse->name],
            ],
            'warehouse' => $warehouse,
        ]);
    }

    public function edit(Warehouse $warehouse): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('warehouses.manage'), 403);

        return view('admin.warehouses.edit', [
            'title' => 'Edit warehouse',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Warehouses', 'href' => route('admin.warehouses.index')],
                ['label' => $warehouse->name, 'href' => route('admin.warehouses.show', $warehouse)],
                ['label' => 'Edit'],
            ],
            'warehouse' => $warehouse,
        ]);
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        $this->warehouses->update($warehouse, $request->validated());

        return redirect()
            ->route('admin.warehouses.show', $warehouse)
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('warehouses.manage'), 403);

        $this->warehouses->delete($warehouse);

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }
}
