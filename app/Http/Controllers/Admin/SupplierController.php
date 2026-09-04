<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupplierRequest;
use App\Http\Requests\Admin\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\Admin\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $suppliers) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('suppliers.view'), 403);

        return view('admin.suppliers.index', [
            'title' => 'Suppliers',
            'breadcrumbs' => [
                ['label' => 'Procurement'],
                ['label' => 'Suppliers'],
            ],
            'suppliers' => $this->suppliers->list([
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ]),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('suppliers.create'), 403);

        return view('admin.suppliers.create', [
            'title' => 'Create supplier',
            'breadcrumbs' => [
                ['label' => 'Procurement'],
                ['label' => 'Suppliers', 'href' => route('admin.suppliers.index')],
                ['label' => 'Create'],
            ],
        ]);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $supplier = $this->suppliers->create($request->validated());

        return redirect()
            ->route('admin.suppliers.show', $supplier)
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('suppliers.view'), 403);

        $canViewPurchases = Auth::guard('admin')->user()?->hasPermission('suppliers.purchases.view') ?? false;

        return view('admin.suppliers.show', [
            'title' => $supplier->name,
            'breadcrumbs' => [
                ['label' => 'Procurement'],
                ['label' => 'Suppliers', 'href' => route('admin.suppliers.index')],
                ['label' => $supplier->name],
            ],
            'supplier' => $supplier,
            'purchaseSummary' => $canViewPurchases
                ? $this->suppliers->purchaseSummary($supplier)
                : null,
            'canViewPurchases' => $canViewPurchases,
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('suppliers.edit'), 403);

        return view('admin.suppliers.edit', [
            'title' => 'Edit supplier',
            'breadcrumbs' => [
                ['label' => 'Procurement'],
                ['label' => 'Suppliers', 'href' => route('admin.suppliers.index')],
                ['label' => $supplier->name, 'href' => route('admin.suppliers.show', $supplier)],
                ['label' => 'Edit'],
            ],
            'supplier' => $supplier,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->suppliers->update($supplier, $request->validated());

        return redirect()
            ->route('admin.suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('suppliers.delete'), 403);

        $this->suppliers->delete($supplier);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
