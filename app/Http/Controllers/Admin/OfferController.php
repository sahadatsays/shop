<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OfferRequest;
use App\Models\Discount;
use App\Models\Offer;
use App\Services\Admin\OfferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function __construct(private OfferService $offers) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('offers.view'), 403);

        return view('admin.offers.index', [
            'title' => 'Offers',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Offers']],
            'offers' => $this->offers->list(['search' => $request->string('search')->toString() ?: null]),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('offers.manage'), 403);

        return view('admin.offers.create', [
            'title' => 'Create Offer',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Offers', 'href' => route('admin.offers.index')], ['label' => 'Create']],
            'discounts' => Discount::query()->orderBy('name')->get(),
            'products' => $this->offers->productOptions(),
        ]);
    }

    public function store(OfferRequest $request): RedirectResponse
    {
        $this->offers->create($request->validated(), $request->file('image'));

        return redirect()->route('admin.offers.index')->with('success', 'Offer created successfully.');
    }

    public function show(Offer $offer): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('offers.view'), 403);

        return view('admin.offers.show', [
            'title' => $offer->name,
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Offers', 'href' => route('admin.offers.index')], ['label' => $offer->name]],
            'offer' => $this->offers->find($offer->id),
        ]);
    }

    public function edit(Offer $offer): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('offers.manage'), 403);
        $offer = $this->offers->find($offer->id);

        return view('admin.offers.edit', [
            'title' => 'Edit Offer',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Offers', 'href' => route('admin.offers.index')], ['label' => $offer->name]],
            'offer' => $offer,
            'discounts' => Discount::query()->orderBy('name')->get(),
            'products' => $this->offers->productOptions(),
        ]);
    }

    public function update(OfferRequest $request, Offer $offer): RedirectResponse
    {
        $this->offers->update($offer, $request->validated(), $request->file('image'));

        return redirect()->route('admin.offers.show', $offer)->with('success', 'Offer updated successfully.');
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('offers.manage'), 403);
        $this->offers->delete($offer);

        return redirect()->route('admin.offers.index')->with('success', 'Offer deleted successfully.');
    }
}
