<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingsRequest;
use App\Services\Admin\StoreSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoreSettingsController extends Controller
{
    public function __construct(private StoreSettingsService $settings) {}

    public function edit(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('settings.view'), 403);

        $options = $this->settings->formOptions();

        return view('admin.settings.edit', [
            'title' => 'Store Settings',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Store Settings'],
            ],
            'settings' => $this->settings->get(),
            'currencies' => $options['currencies'],
            'timezones' => $options['timezones'],
            'themeColorFields' => $options['themeColorFields'],
        ]);
    }

    public function update(StoreSettingsRequest $request): RedirectResponse
    {
        $this->settings->update(
            $request->validated(),
            $request->file('logo'),
            $request->file('favicon'),
            $request->file('og_image'),
        );

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Store settings saved successfully.');
    }
}
