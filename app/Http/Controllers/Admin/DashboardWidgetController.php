<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Admin\DashboardWidgetType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DashboardWidgetRequest;
use App\Models\DashboardWidget;
use App\Models\Permission;
use App\Models\Role;
use App\Support\Dashboard\WidgetRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardWidgetController extends Controller
{
    public function __construct(private readonly WidgetRegistry $registry) {}

    public function index(): View
    {
        $widgets = DashboardWidget::query()->ordered()->with('roles:id,name')->get();

        return view('admin.dashboard-widgets.index', [
            'title' => 'Dashboard Widgets',
            'breadcrumbs' => [['label' => 'Dashboard Widgets']],
            'widgets' => $widgets,
            'registeredKeys' => $this->registry->keys(),
        ]);
    }

    public function create(): View
    {
        return view('admin.dashboard-widgets.create', [
            'title' => 'Create Widget',
            'breadcrumbs' => [['label' => 'Dashboard Widgets', 'url' => route('admin.dashboard-widgets.index')], ['label' => 'Create']],
            'widget' => new DashboardWidget(['width' => 6, 'height' => 1, 'display_order' => 0, 'is_active' => true]),
            'types' => DashboardWidgetType::options(),
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('group')->orderBy('name')->get(),
            'selectedRoles' => [],
            'registeredKeys' => $this->registry->keys(),
        ]);
    }

    public function store(DashboardWidgetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $widget = DashboardWidget::query()->create($data);
        $widget->roles()->sync($roles);

        return redirect()
            ->route('admin.dashboard-widgets.index')
            ->with('status', "Widget \"{$widget->name}\" created.");
    }

    public function edit(DashboardWidget $dashboardWidget): View
    {
        return view('admin.dashboard-widgets.edit', [
            'title' => 'Edit Widget',
            'breadcrumbs' => [['label' => 'Dashboard Widgets', 'url' => route('admin.dashboard-widgets.index')], ['label' => $dashboardWidget->name]],
            'widget' => $dashboardWidget,
            'types' => DashboardWidgetType::options(),
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('group')->orderBy('name')->get(),
            'selectedRoles' => $dashboardWidget->roles()->pluck('roles.id')->all(),
            'registeredKeys' => $this->registry->keys(),
        ]);
    }

    public function update(DashboardWidgetRequest $request, DashboardWidget $dashboardWidget): RedirectResponse
    {
        $data = $request->validated();
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $dashboardWidget->update($data);
        $dashboardWidget->roles()->sync($roles);

        return redirect()
            ->route('admin.dashboard-widgets.index')
            ->with('status', "Widget \"{$dashboardWidget->name}\" updated.");
    }

    public function toggle(DashboardWidget $dashboardWidget): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('dashboard.widgets.manage'), 403);

        $dashboardWidget->update(['is_active' => ! $dashboardWidget->is_active]);

        return back()->with('status', "Widget \"{$dashboardWidget->name}\" ".($dashboardWidget->is_active ? 'enabled' : 'disabled').'.');
    }

    public function destroy(DashboardWidget $dashboardWidget): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('dashboard.widgets.manage'), 403);
        abort_if($dashboardWidget->is_system, 403, 'System widgets cannot be deleted.');

        $name = $dashboardWidget->name;
        $dashboardWidget->delete();

        return redirect()
            ->route('admin.dashboard-widgets.index')
            ->with('status', "Widget \"{$name}\" deleted.");
    }
}
