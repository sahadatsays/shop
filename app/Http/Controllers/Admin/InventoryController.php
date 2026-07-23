<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustStockRequest;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\Admin\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventory) {}

    public function index(Request $request): View
    {
        return view('admin.inventory.index', [
            'title' => 'Inventory',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Inventory'],
            ],
            'products' => $this->inventory->listProducts([
                'search' => $request->string('search')->toString() ?: null,
                'stock_status' => $request->string('stock_status')->toString() ?: null,
                'warehouse_id' => $request->integer('warehouse_id') ?: null,
            ]),
            'summary' => $this->inventory->summary(),
            'warehouses' => $this->inventory->activeWarehouses(),
            'filters' => $request->only(['search', 'stock_status', 'warehouse_id']),
        ]);
    }

    public function movements(Request $request): View
    {
        return view('admin.inventory.movements', [
            'title' => 'Stock History',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Inventory', 'href' => route('admin.inventory.index')],
                ['label' => 'Stock history'],
            ],
            'movements' => $this->inventory->listMovements([
                'search' => $request->string('search')->toString() ?: null,
                'type' => $request->string('type')->toString() ?: null,
                'product_id' => $request->integer('product_id') ?: null,
                'warehouse_id' => $request->integer('warehouse_id') ?: null,
            ]),
            'warehouses' => $this->inventory->activeWarehouses(),
            'filters' => $request->only(['search', 'type', 'product_id', 'warehouse_id']),
        ]);
    }

    public function show(Product $product): View
    {
        $product = $this->inventory->showProduct($product->id);

        return view('admin.inventory.show', [
            'title' => $product->name,
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Inventory', 'href' => route('admin.inventory.index')],
                ['label' => $product->name],
            ],
            'product' => $product,
            'warehouses' => $this->inventory->activeWarehouses(),
        ]);
    }

    public function adjust(Product $product): View
    {
        return view('admin.inventory.adjust', [
            'title' => 'Adjust Stock',
            'breadcrumbs' => [
                ['label' => 'Catalog'],
                ['label' => 'Inventory', 'href' => route('admin.inventory.index')],
                ['label' => $product->name, 'href' => route('admin.inventory.show', $product)],
                ['label' => 'Adjust'],
            ],
            'product' => $product->load(['warehouseStock.warehouse']),
            'warehouses' => $this->inventory->activeWarehouses(),
        ]);
    }

    public function storeAdjustment(AdjustStockRequest $request, Product $product): RedirectResponse
    {
        $warehouse = Warehouse::query()->findOrFail($request->integer('warehouse_id'));

        try {
            $this->inventory->adjustStock(
                product: $product,
                warehouse: $warehouse,
                type: $request->enum('type', StockMovementType::class),
                quantity: $request->integer('quantity'),
                reference: $request->string('reference')->toString() ?: null,
                notes: $request->string('notes')->toString() ?: null,
            );
        } catch (\InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.inventory.show', $product)
            ->with('success', 'Stock adjustment recorded successfully.');
    }
}
