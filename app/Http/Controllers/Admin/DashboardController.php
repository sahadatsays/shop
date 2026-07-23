<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Admin\DashboardDateRange;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DashboardPreferenceRequest;
use App\Services\Admin\Dashboard\DashboardComposer;
use App\Services\Admin\Dashboard\WidgetPreferenceService;
use App\Support\Dashboard\WidgetContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardComposer $composer,
        private readonly WidgetPreferenceService $preferences,
    ) {}

    public function index(Request $request): View
    {
        $range = $this->resolveRange($request);
        $user = Auth::guard('admin')->user();

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'breadcrumbs' => [],
            'widgets' => $this->composer->widgetsFor($user),
            'range' => $range,
            'rangeOptions' => DashboardDateRange::options(),
            'from' => $request->query('from', session('dashboard.from')),
            'to' => $request->query('to', session('dashboard.to')),
            'gridColumns' => (int) config('dashboard.grid_columns', 12),
        ]);
    }

    /**
     * Async render of a single widget body (lazy load + refresh).
     */
    public function widget(Request $request, string $key): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $resolved = $this->composer->resolveWidget($user, $key);

        abort_if($resolved === null, 404);
        abort_unless($resolved->hasProvider, 404);

        $range = $this->resolveRange($request, persist: false);
        $context = WidgetContext::make($user, $range, $request->query('from'), $request->query('to'));

        $data = $this->composer->computeData($resolved, $context);
        $view = $this->composer->viewFor($key);

        $html = view($view, [
            'data' => $data,
            'widget' => $resolved,
            'context' => $context,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function savePreferences(DashboardPreferenceRequest $request): JsonResponse
    {
        $user = Auth::guard('admin')->user();

        $this->preferences->saveLayout($user, $request->validated('widgets'));

        return response()->json(['status' => 'saved']);
    }

    public function resetPreferences(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('admin')->user();
        $this->preferences->reset($user);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'reset']);
        }

        return redirect()->route('admin.dashboard')->with('status', 'Dashboard layout reset.');
    }

    private function resolveRange(Request $request, bool $persist = true): DashboardDateRange
    {
        $key = $request->query('range', session('dashboard.range', config('dashboard.default_range', 'last_30_days')));
        $range = DashboardDateRange::fromKey($key);

        if ($persist) {
            session([
                'dashboard.range' => $range->value,
                'dashboard.from' => $request->query('from'),
                'dashboard.to' => $request->query('to'),
            ]);
        }

        return $range;
    }
}
