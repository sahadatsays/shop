<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromoBannerRequest;
use App\Models\PromoBanner;
use App\Services\Admin\PromoBannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PromoBannerController extends Controller
{
    public function __construct(private PromoBannerService $banners) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        return view('admin.homepage.promo-banners.index', [
            'title' => 'Promo Banners',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Promo Banners'],
            ],
            'banners' => $this->banners->list([
                'search' => $request->string('search')->toString() ?: null,
            ]),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.promo-banners.create', [
            'title' => 'Create Promo Banner',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Promo Banners', 'href' => route('admin.homepage.promo-banners.index')],
                ['label' => 'Create'],
            ],
        ]);
    }

    public function store(PromoBannerRequest $request): RedirectResponse
    {
        $this->banners->create($request->validated(), $request->file('image'));

        return redirect()
            ->route('admin.homepage.promo-banners.index')
            ->with('success', 'Promo banner created successfully.');
    }

    public function edit(PromoBanner $promoBanner): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.promo-banners.edit', [
            'title' => 'Edit Promo Banner',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Promo Banners', 'href' => route('admin.homepage.promo-banners.index')],
                ['label' => $promoBanner->title],
            ],
            'banner' => $promoBanner,
        ]);
    }

    public function update(PromoBannerRequest $request, PromoBanner $promoBanner): RedirectResponse
    {
        $this->banners->update($promoBanner, $request->validated(), $request->file('image'));

        return redirect()
            ->route('admin.homepage.promo-banners.index')
            ->with('success', 'Promo banner updated successfully.');
    }

    public function destroy(PromoBanner $promoBanner): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        $this->banners->delete($promoBanner);

        return redirect()
            ->route('admin.homepage.promo-banners.index')
            ->with('success', 'Promo banner deleted successfully.');
    }
}
