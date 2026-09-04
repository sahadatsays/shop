<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\User;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private AdminUserService $adminUsers) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('users.view'), 403);

        return view('admin.users.index', [
            'title' => 'Admin users',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Admin users'],
            ],
            'users' => $this->adminUsers->list([
                'search' => $request->string('search')->toString() ?: null,
                'is_active' => $request->string('is_active')->toString() ?: null,
                'role_id' => $request->integer('role_id') ?: null,
            ]),
            'roles' => $this->adminUsers->roleOptions(Auth::guard('admin')->user()),
            'filters' => $request->only(['search', 'is_active', 'role_id']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('users.manage'), 403);

        return view('admin.users.create', [
            'title' => 'Create admin user',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Admin users', 'href' => route('admin.users.index')],
                ['label' => 'Create'],
            ],
            'roles' => $this->adminUsers->roleOptions(Auth::guard('admin')->user()),
        ]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $user = $this->adminUsers->create(
            $request->validated(),
            Auth::guard('admin')->user(),
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Admin user created successfully.');
    }

    public function show(User $user): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('users.view'), 403);

        $user = $this->adminUsers->find($user->id);

        return view('admin.users.show', [
            'title' => $user->name,
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Admin users', 'href' => route('admin.users.index')],
                ['label' => $user->name],
            ],
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('users.manage'), 403);

        $user = $this->adminUsers->find($user->id);

        return view('admin.users.edit', [
            'title' => 'Edit admin user',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Admin users', 'href' => route('admin.users.index')],
                ['label' => $user->name, 'href' => route('admin.users.show', $user)],
                ['label' => 'Edit'],
            ],
            'user' => $user,
            'roles' => $this->adminUsers->roleOptions(Auth::guard('admin')->user()),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $this->adminUsers->update(
            $user,
            $request->validated(),
            Auth::guard('admin')->user(),
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Admin user updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('users.manage'), 403);

        $this->adminUsers->delete($user, Auth::guard('admin')->user());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user deleted successfully.');
    }
}
