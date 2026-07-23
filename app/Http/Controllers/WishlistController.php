<?php

namespace App\Http\Controllers;

use App\DTOs\Wishlist\WishlistSummary;
use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\InsufficientStockException;
use App\Exceptions\Wishlist\WishlistValidationException;
use App\Http\Requests\AddToWishlistRequest;
use App\Models\WishlistItem;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        private WishlistService $wishlist,
        private CartService $cart,
    ) {}

    public function index(): View
    {
        return view('wishlist', [
            'summary' => $this->wishlist->summary(),
        ]);
    }

    public function store(AddToWishlistRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $summary = $this->wishlist->addItem((int) $request->validated('product_id'));
        } catch (WishlistValidationException $exception) {
            return $this->errorResponse($request, $exception->getMessage(), 422);
        }

        return $this->successResponse($request, $summary, 'Item saved to your wishlist.');
    }

    public function toggle(AddToWishlistRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $result = $this->wishlist->toggle((int) $request->validated('product_id'));
        } catch (WishlistValidationException $exception) {
            return $this->errorResponse($request, $exception->getMessage(), 422);
        }

        $message = $result['in_wishlist']
            ? 'Item saved to your wishlist.'
            : 'Item removed from your wishlist.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'in_wishlist' => $result['in_wishlist'],
                'wishlist' => $this->wishlistPayload($result['summary']),
                'cart' => [
                    'item_count' => $this->cart->itemCount(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(WishlistItem $wishlistItem): JsonResponse|RedirectResponse
    {
        $summary = $this->wishlist->removeItem($wishlistItem);

        return $this->successResponse(request(), $summary, 'Item removed from your wishlist.');
    }

    public function moveToCart(WishlistItem $wishlistItem): JsonResponse|RedirectResponse
    {
        try {
            $summary = $this->wishlist->moveToCart($wishlistItem);
        } catch (WishlistValidationException|CartValidationException|InsufficientStockException $exception) {
            return $this->errorResponse(request(), $exception->getMessage(), 422);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Item moved to cart.',
                'wishlist' => $this->wishlistPayload($summary),
                'cart' => [
                    'item_count' => $this->cart->itemCount(),
                ],
            ]);
        }

        return redirect()
            ->route('cart')
            ->with('success', 'Item moved to cart.');
    }

    public function moveAllToCart(): JsonResponse|RedirectResponse
    {
        $result = $this->wishlist->moveAllToCart();

        $message = match (true) {
            $result['moved'] === 0 => 'No in-stock items were available to move.',
            $result['skipped'] > 0 => "{$result['moved']} items moved to cart. {$result['skipped']} could not be added.",
            default => 'All available items moved to cart.',
        };

        if (request()->expectsJson()) {
            return response()->json([
                'message' => $message,
                'moved' => $result['moved'],
                'skipped' => $result['skipped'],
                'wishlist' => $this->wishlistPayload($result['summary']),
                'cart' => [
                    'item_count' => $this->cart->itemCount(),
                ],
            ]);
        }

        return redirect()
            ->route('cart')
            ->with('success', $message);
    }

    public function clear(): JsonResponse|RedirectResponse
    {
        $summary = $this->wishlist->clear();

        return $this->successResponse(request(), $summary, 'Wishlist cleared.');
    }

    private function successResponse(
        Request $request,
        WishlistSummary $summary,
        ?string $message = null,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'wishlist' => $this->wishlistPayload($summary),
            ]);
        }

        return back()->with('success', $message ?? 'Wishlist updated.');
    }

    private function errorResponse(
        Request $request,
        string $message,
        int $status,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withErrors(['wishlist' => $message]);
    }

    /**
     * @return array<string, mixed>
     */
    private function wishlistPayload(WishlistSummary $summary): array
    {
        return [
            'item_count' => $summary->itemCount,
            'product_ids' => $summary->productIds(),
            'is_empty' => $summary->isEmpty(),
        ];
    }
}
