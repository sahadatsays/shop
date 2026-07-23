<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePermissionMatrixRequest;
use App\Services\Admin\PermissionMatrixService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermissionMatrixController extends Controller
{
    public function __construct(private PermissionMatrixService $matrix) {}

    public function edit(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('access-matrix.manage'), 403);

        $data = $this->matrix->data();

        return view('admin.roles.matrix', [
            'title' => 'Permission Matrix',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Permission Matrix'],
            ],
            ...$data,
        ]);
    }

    public function update(UpdatePermissionMatrixRequest $request): RedirectResponse
    {
        $assignments = [];

        foreach ($request->input('matrix', []) as $roleId => $permissions) {
            $assignments[(int) $roleId] = collect($permissions)
                ->filter(fn ($enabled): bool => (bool) $enabled)
                ->keys()
                ->map(fn ($permissionId): int => (int) $permissionId)
                ->all();
        }

        $this->matrix->sync($assignments);

        return redirect()
            ->route('admin.roles.matrix')
            ->with('success', 'Permission matrix updated successfully.');
    }
}
