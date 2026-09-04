<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RefundReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessRefundRequest;
use App\Models\Order;
use App\Models\Refund;
use App\Services\RefundService;
use App\Support\MoneyFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function __construct(private RefundService $refunds) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('refunds.view'), 403);

        $filters = [
            'search' => $request->string('search')->toString() ?: null,
            'status' => $request->string('status')->toString() ?: null,
            'filter' => $request->string('filter')->toString() ?: null,
        ];

        $pendingReturnOrders = null;

        if ($filters['filter'] === 'pending_returns') {
            $pendingReturnOrders = Order::query()
                ->with('customer')
                ->needsRefundAttention()
                ->latest('return_requested_at')
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.refunds.index', [
            'title' => 'Refunds',
            'breadcrumbs' => [
                ['label' => 'Commerce'],
                ['label' => 'Refunds'],
            ],
            'refunds' => $this->refunds->list($filters),
            'pendingReturnOrders' => $pendingReturnOrders,
            'summary' => $this->refunds->summary(),
            'filters' => $filters,
        ]);
    }

    public function show(Refund $refund): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('refunds.view'), 403);

        $refund = $this->refunds->find($refund->id);

        return view('admin.refunds.show', [
            'title' => 'Refund #'.$refund->id,
            'breadcrumbs' => [
                ['label' => 'Commerce'],
                ['label' => 'Refunds', 'href' => route('admin.refunds.index')],
                ['label' => 'Refund #'.$refund->id],
            ],
            'refund' => $refund,
        ]);
    }

    public function store(ProcessRefundRequest $request, Order $order): RedirectResponse
    {
        $refund = $this->refunds->processRefund(
            order: $order,
            amountCents: $request->amountCents(),
            reason: $request->enum('reason', RefundReason::class),
            notes: $request->string('notes')->toString() ?: null,
            restoreStock: $request->boolean('restore_stock'),
            admin: Auth::guard('admin')->user(),
        );

        return redirect()
            ->route('admin.refunds.show', $refund)
            ->with('success', 'Refund of '.MoneyFormatter::format($refund->amount_cents).' processed successfully.');
    }
}
