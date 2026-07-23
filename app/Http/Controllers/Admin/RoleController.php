<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private RoleService $roles) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('roles.view'), 403);

        return view('admin.roles.index', [
            'title' => 'Roles',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Roles'],
            ],
            'roles' => $this->roles->list([
                'search' => $request->string('search')->toString() ?: null,
            ]),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('roles.manage'), 403);

        return view('admin.roles.create', [
            'title' => 'Create Role',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Roles', 'href' => route('admin.roles.index')],
                ['label' => 'Create'],
            ],
            'permissions' => $this->roles->permissionOptions(),
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->roles->create($request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('roles.view'), 403);

        $role = $this->roles->find($role->id);

        return view('admin.roles.show', [
            'title' => $role->name,
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Roles', 'href' => route('admin.roles.index')],
                ['label' => $role->name],
            ],
            'role' => $role,
        ]);
    }

    public function edit(Role $role): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('roles.manage'), 403);

        $role = $this->roles->find($role->id);

        return view('admin.roles.edit', [
            'title' => 'Edit Role',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Roles', 'href' => route('admin.roles.index')],
                ['label' => $role->name, 'href' => route('admin.roles.show', $role)],
                ['label' => 'Edit'],
            ],
            'role' => $role,
            'permissions' => $this->roles->permissionOptions(),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->roles->update($role, $request->validated());

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('roles.manage'), 403);

        $this->roles->delete($role);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
