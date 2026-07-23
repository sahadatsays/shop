<?php

namespace App\Http\Controllers;

use App\DTOs\Cart\CartSummary;
use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\InsufficientStockException;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function index(): View
    {
        $summary = $this->cart->summary();

        try {
            $this->cart->validateCart($summary->cart);
            $this->cart->validateStock($summary->cart);
        } catch (CartValidationException|InsufficientStockException $exception) {
            session()->flash('cart_warning', $exception->getMessage());
            $summary = $this->cart->summary();
        }

        $recommended = Product::query()
            ->published()
            ->inStock()
            ->with('images')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('cart', [
            'summary' => $summary,
            'recommended' => $recommended,
        ]);
    }

    public function store(AddToCartRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $summary = $this->cart->addItem(
                (int) $request->validated('product_id'),
                (int) $request->validated('quantity', 1),
            );
        } catch (CartValidationException|InsufficientStockException $exception) {
            return $this->errorResponse($request, $exception->getMessage(), 422);
        }

        return $this->successResponse($request, $summary, 'Item added to cart.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse|RedirectResponse
    {
        try {
            $summary = $this->cart->updateQuantity(
                $cartItem,
                (int) $request->validated('quantity'),
            );
        } catch (CartValidationException|InsufficientStockException $exception) {
            return $this->errorResponse($request, $exception->getMessage(), 422);
        }

        return $this->successResponse($request, $summary);
    }

    public function destroy(CartItem $cartItem): JsonResponse|RedirectResponse
    {
        $summary = $this->cart->removeItem($cartItem);

        return $this->successResponse(request(), $summary, 'Item removed from cart.');
    }

    public function save(): JsonResponse|RedirectResponse
    {
        try {
            $summary = $this->cart->saveCart();
        } catch (CartValidationException $exception) {
            return $this->errorResponse(request(), $exception->getMessage(), 422);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Cart saved for later.',
                'cart' => $this->cartPayload($summary),
            ]);
        }

        return back()->with('success', 'Cart saved for later.');
    }

    public function validateCart(): JsonResponse
    {
        try {
            $this->cart->validateCart();
            $this->cart->validateStock();
            $summary = $this->cart->summary();
        } catch (CartValidationException|InsufficientStockException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'valid' => false,
            ], 422);
        }

        return response()->json([
            'message' => 'Cart is valid.',
            'valid' => true,
            'cart' => $this->cartPayload($summary),
        ]);
    }

    private function successResponse(
        Request $request,
        CartSummary $summary,
        ?string $message = null,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'cart' => $this->cartPayload($summary),
            ]);
        }

        return back()->with('success', $message ?? 'Cart updated.');
    }

    private function errorResponse(
        Request $request,
        string $message,
        int $status,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withErrors(['cart' => $message]);
    }

    /**
     * @return array<string, mixed>
     */
    private function cartPayload(CartSummary $summary): array
    {
        return [
            'item_count' => $summary->itemCount,
            'subtotal' => $summary->formattedSubtotal(),
            'subtotal_cents' => $summary->subtotalCents,
            'shipping' => $summary->formattedShipping(),
            'tax' => $summary->formattedTax(),
            'total' => $summary->formattedTotal(),
            'is_empty' => $summary->isEmpty(),
            'free_shipping' => $summary->qualifiesForFreeShipping(),
        ];
    }
}
