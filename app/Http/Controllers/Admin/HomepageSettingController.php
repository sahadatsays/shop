<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomepageSettingRequest;
use App\Services\Admin\HomepageSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomepageSettingController extends Controller
{
    public function __construct(private HomepageSettingService $settings) {}

    public function edit(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        $settings = $this->settings->get();

        return view('admin.homepage.settings.edit', [
            'title' => 'Homepage Settings',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Homepage Settings'],
            ],
            'settings' => $settings,
            'sectionLabels' => $this->settings->sectionLabels(),
            'enabledSections' => $settings->enabledSectionKeys(),
        ]);
    }

    public function update(HomepageSettingRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());

        return redirect()
            ->route('admin.homepage.settings.edit')
            ->with('success', 'Homepage settings saved successfully.');
    }
}
