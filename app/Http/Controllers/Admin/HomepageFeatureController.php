<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomepageFeatureRequest;
use App\Models\HomepageFeature;
use App\Services\Admin\HomepageFeatureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomepageFeatureController extends Controller
{
    public function __construct(private HomepageFeatureService $features) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        return view('admin.homepage.features.index', [
            'title' => 'Why Shop Features',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Why Shop Features'],
            ],
            'features' => $this->features->list([
                'search' => $request->string('search')->toString() ?: null,
            ]),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.features.create', [
            'title' => 'Create Feature',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Why Shop Features', 'href' => route('admin.homepage.features.index')],
                ['label' => 'Create'],
            ],
        ]);
    }

    public function store(HomepageFeatureRequest $request): RedirectResponse
    {
        $this->features->create($request->validated());

        return redirect()
            ->route('admin.homepage.features.index')
            ->with('success', 'Feature created successfully.');
    }

    public function edit(HomepageFeature $feature): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.features.edit', [
            'title' => 'Edit Feature',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Why Shop Features', 'href' => route('admin.homepage.features.index')],
                ['label' => $feature->title],
            ],
            'feature' => $feature,
        ]);
    }

    public function update(HomepageFeatureRequest $request, HomepageFeature $feature): RedirectResponse
    {
        $this->features->update($feature, $request->validated());

        return redirect()
            ->route('admin.homepage.features.index')
            ->with('success', 'Feature updated successfully.');
    }

    public function destroy(HomepageFeature $feature): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        $this->features->delete($feature);

        return redirect()
            ->route('admin.homepage.features.index')
            ->with('success', 'Feature deleted successfully.');
    }
}
