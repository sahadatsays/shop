<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('audit.view'), 403);

        return view('admin.audit-logs.index', [
            'title' => 'Audit logs',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Audit logs'],
            ],
            'logs' => $this->audit->paginate([
                'category' => $request->string('category')->toString() ?: null,
                'action' => $request->string('action')->toString() ?: null,
                'search' => $request->string('search')->toString() ?: null,
            ]),
            'filters' => $request->only(['category', 'action', 'search']),
        ]);
    }
}
