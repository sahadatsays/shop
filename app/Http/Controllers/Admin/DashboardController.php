<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function __invoke(): View
    {
        $dashboard = $this->dashboard->getViewData();

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'breadcrumbs' => [],
            'dashboard' => $dashboard,
        ]);
    }
}
