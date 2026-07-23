<?php

namespace App\Http\Controllers;

use App\DTOs\Compare\CompareSummary;
use App\Exceptions\Compare\CompareValidationException;
use App\Http\Requests\AddToCompareRequest;
use App\Models\CompareItem;
use App\Services\CompareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    public function __construct(
        private CompareService $compare,
    ) {}

    public function index(): View
    {
        return view('compare', [
            'pageData' => $this->compare->pageData(),
            'maxItems' => $this->compare->maxItems(),
        ]);
    }

    public function store(AddToCompareRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $summary = $this->compare->addItem((int) $request->validated('product_id'));
        } catch (CompareValidationException $exception) {
            return $this->errorResponse($request, $exception->getMessage(), 422);
        }

        return $this->successResponse($request, $summary, 'Product added to compare.');
    }

    public function toggle(AddToCompareRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $result = $this->compare->toggle((int) $request->validated('product_id'));
        } catch (CompareValidationException $exception) {
            return $this->errorResponse($request, $exception->getMessage(), 422);
        }

        $message = $result['in_compare']
            ? 'Product added to compare.'
            : 'Product removed from compare.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'in_compare' => $result['in_compare'],
                'compare' => $this->comparePayload($result['summary']),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(CompareItem $compareItem): JsonResponse|RedirectResponse
    {
        $summary = $this->compare->removeItem($compareItem);

        return $this->successResponse(request(), $summary, 'Product removed from compare.');
    }

    public function clear(): JsonResponse|RedirectResponse
    {
        $summary = $this->compare->clear();

        return $this->successResponse(request(), $summary, 'Compare list cleared.');
    }

    private function successResponse(
        Request $request,
        CompareSummary $summary,
        ?string $message = null,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'compare' => $this->comparePayload($summary),
            ]);
        }

        return back()->with('success', $message ?? 'Compare list updated.');
    }

    private function errorResponse(
        Request $request,
        string $message,
        int $status,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withErrors(['compare' => $message]);
    }

    /**
     * @return array<string, mixed>
     */
    private function comparePayload(CompareSummary $summary): array
    {
        return [
            'item_count' => $summary->itemCount,
            'product_ids' => $summary->productIds(),
            'max_items' => $this->compare->maxItems(),
            'is_empty' => $summary->isEmpty(),
        ];
    }
}
