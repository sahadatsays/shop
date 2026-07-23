<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionRequest;
use App\Models\Permission;
use App\Services\Admin\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct(private PermissionService $permissions) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('permissions.view'), 403);

        return view('admin.permissions.index', [
            'title' => 'Permissions',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Permissions'],
            ],
            'permissions' => $this->permissions->list([
                'search' => $request->string('search')->toString() ?: null,
                'group' => $request->string('group')->toString() ?: null,
            ]),
            'groups' => $this->permissions->groups(),
            'filters' => $request->only(['search', 'group']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('permissions.manage'), 403);

        return view('admin.permissions.create', [
            'title' => 'Create Permission',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Permissions', 'href' => route('admin.permissions.index')],
                ['label' => 'Create'],
            ],
            'groups' => $this->permissions->groups(),
        ]);
    }

    public function store(PermissionRequest $request): RedirectResponse
    {
        $this->permissions->create($request->validated());

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function show(Permission $permission): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('permissions.view'), 403);

        $permission = $this->permissions->find($permission->id);

        return view('admin.permissions.show', [
            'title' => $permission->name,
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Permissions', 'href' => route('admin.permissions.index')],
                ['label' => $permission->name],
            ],
            'permission' => $permission,
        ]);
    }

    public function edit(Permission $permission): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('permissions.manage'), 403);

        $permission = $this->permissions->find($permission->id);

        return view('admin.permissions.edit', [
            'title' => 'Edit Permission',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Permissions', 'href' => route('admin.permissions.index')],
                ['label' => $permission->name, 'href' => route('admin.permissions.show', $permission)],
                ['label' => 'Edit'],
            ],
            'permission' => $permission,
            'groups' => $this->permissions->groups(),
        ]);
    }

    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissions->update($permission, $request->validated());

        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('permissions.manage'), 403);

        $this->permissions->delete($permission);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
