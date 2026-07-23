<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PromotionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotionRequest;
use App\Models\Offer;
use App\Models\Promotion;
use App\Services\Admin\CollectionService;
use App\Services\Admin\PromotionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BannerPromotionController extends Controller
{
    public function __construct(
        private PromotionService $promotions,
        private CollectionService $collections,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('promotions.view'), 403);

        return view('admin.promotions.banners.index', [
            'title' => 'Banner Promotions',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Banner Promotions']],
            'promotions' => $this->promotions->list(PromotionType::Banner, ['search' => $request->string('search')->toString() ?: null]),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('promotions.manage'), 403);

        return view('admin.promotions.banners.create', [
            'title' => 'Create Banner',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Banner Promotions', 'href' => route('admin.banner-promotions.index')], ['label' => 'Create']],
            'collections' => $this->collections->options(),
            'offers' => Offer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(PromotionRequest $request): RedirectResponse
    {
        $this->promotions->create(PromotionType::Banner, $request->validated(), $request->file('image'));

        return redirect()->route('admin.banner-promotions.index')->with('success', 'Banner promotion created.');
    }

    public function edit(Promotion $promotion): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('promotions.manage'), 403);
        abort_unless($promotion->type === PromotionType::Banner, 404);

        return view('admin.promotions.banners.edit', [
            'title' => 'Edit Banner',
            'breadcrumbs' => [['label' => 'Marketing'], ['label' => 'Banner Promotions', 'href' => route('admin.banner-promotions.index')], ['label' => $promotion->name]],
            'promotion' => $this->promotions->find($promotion->id),
            'collections' => $this->collections->options(),
            'offers' => Offer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        abort_unless($promotion->type === PromotionType::Banner, 404);
        $this->promotions->update($promotion, $request->validated(), $request->file('image'));

        return redirect()->route('admin.banner-promotions.index')->with('success', 'Banner promotion updated.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('promotions.manage'), 403);
        abort_unless($promotion->type === PromotionType::Banner, 404);
        $this->promotions->delete($promotion);

        return redirect()->route('admin.banner-promotions.index')->with('success', 'Banner promotion deleted.');
    }
}
