<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroBannerRequest;
use App\Models\HeroBanner;
use App\Services\Admin\HeroBannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HeroBannerController extends Controller
{
    public function __construct(private HeroBannerService $banners) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        return view('admin.homepage.hero-banners.index', [
            'title' => 'Hero Banners',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Hero Banners'],
            ],
            'banners' => $this->banners->list([
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ]),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.hero-banners.create', [
            'title' => 'Create Hero Banner',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Hero Banners', 'href' => route('admin.homepage.hero-banners.index')],
                ['label' => 'Create'],
            ],
        ]);
    }

    public function store(HeroBannerRequest $request): RedirectResponse
    {
        $this->banners->create(
            $request->validated(),
            $request->file('desktop_image'),
            $request->file('mobile_image'),
        );

        return redirect()
            ->route('admin.homepage.hero-banners.index')
            ->with('success', 'Hero banner created successfully.');
    }

    public function edit(HeroBanner $heroBanner): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.hero-banners.edit', [
            'title' => 'Edit Hero Banner',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Hero Banners', 'href' => route('admin.homepage.hero-banners.index')],
                ['label' => $heroBanner->title],
            ],
            'banner' => $heroBanner,
        ]);
    }

    public function update(HeroBannerRequest $request, HeroBanner $heroBanner): RedirectResponse
    {
        $this->banners->update(
            $heroBanner,
            $request->validated(),
            $request->file('desktop_image'),
            $request->file('mobile_image'),
        );

        return redirect()
            ->route('admin.homepage.hero-banners.index')
            ->with('success', 'Hero banner updated successfully.');
    }

    public function destroy(HeroBanner $heroBanner): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        $this->banners->delete($heroBanner);

        return redirect()
            ->route('admin.homepage.hero-banners.index')
            ->with('success', 'Hero banner deleted successfully.');
    }
}
