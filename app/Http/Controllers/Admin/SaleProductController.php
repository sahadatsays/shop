<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSaleProductRequest;
use App\Models\Product;
use App\Services\Admin\SaleProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SaleProductController extends Controller
{
    public function __construct(private SaleProductService $saleProducts) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('sale-products.view'), 403);

        return view('admin.sale-products.index', [
            'title' => 'Sale Products',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Sale Products']],
            'products' => $this->saleProducts->list([
                'search' => $request->string('search')->toString() ?: null,
                'on_sale' => $request->string('on_sale')->toString() ?: '1',
            ]),
            'filters' => $request->only(['search', 'on_sale']),
        ]);
    }

    public function update(UpdateSaleProductRequest $request, Product $product): RedirectResponse
    {
        $this->saleProducts->updatePricing($product, $request->validated());

        return redirect()->route('admin.sale-products.index')->with('success', 'Sale pricing updated.');
    }
}
