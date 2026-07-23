<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountRequest;
use App\Models\Discount;
use App\Services\Admin\DiscountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function __construct(private DiscountService $discounts) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('discounts.view'), 403);

        return view('admin.discounts.index', [
            'title' => 'Discounts',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Discounts']],
            'discounts' => $this->discounts->list($request->only(['search', 'active'])),
            'filters' => $request->only(['search', 'active']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('discounts.manage'), 403);

        return view('admin.discounts.create', [
            'title' => 'Create Discount',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Discounts', 'href' => route('admin.discounts.index')], ['label' => 'Create']],
        ]);
    }

    public function store(DiscountRequest $request): RedirectResponse
    {
        $this->discounts->create($request->validated());

        return redirect()->route('admin.discounts.index')->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('discounts.manage'), 403);

        return view('admin.discounts.edit', [
            'title' => 'Edit Discount',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Discounts', 'href' => route('admin.discounts.index')], ['label' => $discount->name]],
            'discount' => $this->discounts->find($discount->id),
        ]);
    }

    public function update(DiscountRequest $request, Discount $discount): RedirectResponse
    {
        $this->discounts->update($discount, $request->validated());

        return redirect()->route('admin.discounts.index')->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('discounts.manage'), 403);
        $this->discounts->delete($discount);

        return redirect()->route('admin.discounts.index')->with('success', 'Discount deleted successfully.');
    }
}
