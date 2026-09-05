<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Enums\PurchaseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReceivePurchaseRequest;
use App\Http\Requests\Admin\StorePurchaseRequest;
use App\Http\Requests\Admin\UpdatePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Admin\InventoryService;
use App\Services\Admin\PurchaseReceiveService;
use App\Services\Admin\PurchaseService;
use App\Services\Admin\SupplierService;
use App\Support\MoneyFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchases,
        private PurchaseReceiveService $receiveService,
        private SupplierService $suppliers,
        private InventoryService $inventory,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.view'), 403);

        return view('admin.purchases.index', [
            'title' => 'Purchases',
            'breadcrumbs' => [
                ['label' => 'Purchases'],
            ],
            'purchases' => $this->purchases->list([
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
                'supplier_id' => $request->integer('supplier_id') ?: null,
            ]),
            'filters' => $request->only(['search', 'status', 'supplier_id']),
            'statuses' => PurchaseStatus::cases(),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.create'), 403);

        return view('admin.purchases.create', [
            'title' => 'Create purchase',
            'breadcrumbs' => [
                ['label' => 'Purchases'],
                ['label' => 'Purchases', 'href' => route('admin.purchases.index')],
                ['label' => 'Create'],
            ],
            'suppliers' => $this->suppliers->selectableForPurchase(),
            'currencySymbol' => MoneyFormatter::symbol(),
            'productSearchUrl' => route('admin.purchases.products.search'),
        ]);
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::guard('admin')->user();
        $purchase = $this->purchases->create($request->validated(), $admin);

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Purchase created successfully.');
    }

    public function show(Purchase $purchase): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.view'), 403);

        $purchase = $this->purchases->find($purchase->id);

        return view('admin.purchases.show', [
            'title' => $purchase->purchase_number,
            'breadcrumbs' => [
                ['label' => 'Purchases'],
                ['label' => 'Purchases', 'href' => route('admin.purchases.index')],
                ['label' => $purchase->purchase_number],
            ],
            'purchase' => $purchase,
            'warehouses' => Warehouse::query()->active()->ordered()->get(),
            'defaultWarehouseId' => $this->inventory->defaultWarehouse()->id,
            'idempotencyKey' => (string) str()->uuid(),
        ]);
    }

    public function edit(Purchase $purchase): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.edit'), 403);

        $purchase = $this->purchases->find($purchase->id);

        abort_unless($purchase->status->isEditable(), 403);

        return view('admin.purchases.edit', [
            'title' => 'Edit '.$purchase->purchase_number,
            'breadcrumbs' => [
                ['label' => 'Purchases'],
                ['label' => 'Purchases', 'href' => route('admin.purchases.index')],
                ['label' => $purchase->purchase_number, 'href' => route('admin.purchases.show', $purchase)],
                ['label' => 'Edit'],
            ],
            'purchase' => $purchase,
            'suppliers' => $this->suppliers->selectableForPurchase(),
            'currencySymbol' => MoneyFormatter::symbol(),
            'productSearchUrl' => route('admin.purchases.products.search'),
        ]);
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::guard('admin')->user();
        $purchase = $this->purchases->update($purchase, $request->validated(), $admin);

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Purchase updated successfully.');
    }

    public function submit(Purchase $purchase): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.create'), 403);

        /** @var User $admin */
        $admin = Auth::guard('admin')->user();
        $this->purchases->submit($purchase, $admin);

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Purchase submitted for approval.');
    }

    public function approve(Purchase $purchase): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.approve'), 403);

        /** @var User $admin */
        $admin = Auth::guard('admin')->user();
        $this->purchases->approve($purchase, $admin);

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Purchase approved. Stock can now be received.');
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.cancel'), 403);

        /** @var User $admin */
        $admin = Auth::guard('admin')->user();
        $this->purchases->cancel($purchase, $admin);

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Purchase cancelled.');
    }

    public function receive(ReceivePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::guard('admin')->user();
        $purchase = $this->receiveService->receive($purchase, $request->validated(), $admin);

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with('success', 'Stock received successfully.');
    }

    public function print(Purchase $purchase): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.view'), 403);

        $purchase = $this->purchases->find($purchase->id);

        return view('admin.purchases.print', [
            'purchase' => $purchase,
        ]);
    }

    public function searchProducts(Request $request): JsonResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('purchases.create')
            || Auth::guard('admin')->user()?->hasPermission('purchases.edit'), 403);

        $term = trim($request->string('q')->toString());

        $products = Product::query()
            ->whereIn('status', [ProductStatus::Published, ProductStatus::Draft])
            ->when($term !== '', function ($query) use ($term): void {
                $query->where(function ($builder) use ($term): void {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('barcode', 'like', "%{$term}%");
                });
            })
            ->ordered()
            ->limit(20)
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'cost_cents' => $product->cost_cents ?? 0,
                'price_cents' => $product->price_cents,
                'stock_quantity' => $product->stock_quantity,
                'cost' => MoneyFormatter::format((int) ($product->cost_cents ?? 0)),
            ]);

        return response()->json(['data' => $products]);
    }
}
