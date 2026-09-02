<?php

namespace App\Services;

use App\Contracts\Repositories\WishlistRepositoryInterface;
use App\DTOs\Wishlist\WishlistLineItem;
use App\DTOs\Wishlist\WishlistSummary;
use App\Enums\ProductStatus;
use App\Exceptions\Wishlist\WishlistValidationException;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistService
{
    public function __construct(
        private WishlistRepositoryInterface $wishlists,
        private CartService $cart,
    ) {}

    public function resolve(): Wishlist
    {
        $customerId = Auth::guard('customer')->id();

        if ($customerId) {
            return $this->resolveCustomerWishlist((int) $customerId);
        }

        if ($wishlist = $this->resolveGuestFromSession()) {
            return $this->wishlists->loadWithItems($wishlist);
        }

        return $this->resolveGuestWishlist();
    }

    private function resolveGuestFromSession(): ?Wishlist
    {
        $wishlistId = session('wishlist_id');

        if (! $wishlistId) {
            return null;
        }

        $wishlist = Wishlist::query()->find($wishlistId);

        if (! $wishlist || $wishlist->customer_id !== null) {
            session()->forget('wishlist_id');

            return null;
        }

        return $wishlist;
    }

    public function resolveGuestWishlist(): Wishlist
    {
        $sessionId = session()->getId();
        $wishlist = $this->wishlists->findOrCreateGuest($sessionId);

        session(['wishlist_id' => $wishlist->id]);

        return $this->wishlists->loadWithItems($wishlist);
    }

    public function resolveCustomerWishlist(int $customerId): Wishlist
    {
        $guestWishlist = $this->resolveGuestFromSession()
            ?? $this->wishlists->findGuestBySession(session()->getId());

        $customerWishlist = $this->wishlists->findOrCreateForCustomer($customerId);

        if ($guestWishlist && $guestWishlist->isGuest() && $guestWishlist->id !== $customerWishlist->id) {
            $this->mergeWishlists($guestWishlist, $customerWishlist);
            $customerWishlist = $customerWishlist->fresh();
        }

        session(['wishlist_id' => $customerWishlist->id]);

        return $this->wishlists->loadWithItems($customerWishlist);
    }

    public function mergeGuestIntoCustomer(Customer|int $customer): Wishlist
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;

        return $this->resolveCustomerWishlist($customerId);
    }

    public function mergeWishlists(Wishlist $source, Wishlist $target): Wishlist
    {
        return DB::transaction(function () use ($source, $target): Wishlist {
            $source->load('items');

            foreach ($source->items as $item) {
                $this->wishlists->addItem($target, $item->product_id);
            }

            $this->wishlists->delete($source);

            return $this->wishlists->loadWithItems($target);
        });
    }

    public function addItem(int $productId): WishlistSummary
    {
        $product = $this->findWishlistableProduct($productId);
        $wishlist = $this->resolve();

        $this->wishlists->addItem($wishlist, $product->id);

        return $this->summary($this->wishlists->loadWithItems($wishlist->fresh()));
    }

    public function removeItem(WishlistItem $item): WishlistSummary
    {
        $this->assertWishlistItemOwnership($item);
        $wishlist = $item->wishlist;

        $this->wishlists->removeItem($item);

        return $this->summary($this->wishlists->loadWithItems($wishlist->fresh()));
    }

    /**
     * @return array{in_wishlist: bool, summary: WishlistSummary}
     */
    public function toggle(int $productId): array
    {
        $wishlist = $this->resolve();
        $existing = $wishlist->items->firstWhere('product_id', $productId);

        if ($existing) {
            $this->wishlists->removeItem($existing);

            return [
                'in_wishlist' => false,
                'summary' => $this->summary($this->wishlists->loadWithItems($wishlist->fresh())),
            ];
        }

        $product = $this->findWishlistableProduct($productId);
        $this->wishlists->addItem($wishlist, $product->id);

        return [
            'in_wishlist' => true,
            'summary' => $this->summary($this->wishlists->loadWithItems($wishlist->fresh())),
        ];
    }

    public function moveToCart(WishlistItem $item): WishlistSummary
    {
        $this->assertWishlistItemOwnership($item);

        $product = $item->product;

        if (! $product || $product->trashed() || $product->status !== ProductStatus::Published) {
            throw new WishlistValidationException('This product is no longer available.');
        }

        if ($product->isOutOfStock()) {
            throw new WishlistValidationException("{$product->name} is currently out of stock.");
        }

        $this->cart->addItem($product->id, 1);
        $this->wishlists->removeItem($item);

        return $this->summary($this->wishlists->loadWithItems($item->wishlist->fresh()));
    }

    /**
     * @return array{moved: int, skipped: int, summary: WishlistSummary}
     */
    public function moveAllToCart(): array
    {
        $wishlist = $this->resolve();
        $moved = 0;
        $skipped = 0;

        foreach ($wishlist->items as $item) {
            $product = $item->product;

            if (! $product || $product->trashed() || $product->status !== ProductStatus::Published || $product->isOutOfStock()) {
                $skipped++;

                continue;
            }

            try {
                $this->cart->addItem($product->id, 1);
                $this->wishlists->removeItem($item);
                $moved++;
            } catch (\Throwable) {
                $skipped++;
            }
        }

        return [
            'moved' => $moved,
            'skipped' => $skipped,
            'summary' => $this->summary($this->wishlists->loadWithItems($wishlist->fresh())),
        ];
    }

    public function clear(): WishlistSummary
    {
        $wishlist = $this->resolve();
        $this->wishlists->clear($wishlist);

        return $this->summary($this->wishlists->loadWithItems($wishlist->fresh()));
    }

    public function summary(?Wishlist $wishlist = null): WishlistSummary
    {
        $wishlist ??= $this->resolve();
        $wishlist = $this->wishlists->loadWithItems($wishlist);

        $items = $wishlist->items
            ->filter(fn (WishlistItem $item): bool => $item->product !== null)
            ->map(fn (WishlistItem $item): WishlistLineItem => new WishlistLineItem($item, $item->product));

        return new WishlistSummary(
            wishlist: $wishlist,
            items: $items,
            itemCount: $items->count(),
        );
    }

    public function itemCount(): int
    {
        return $this->summary()->itemCount;
    }

    /**
     * @return list<int>
     */
    public function productIds(): array
    {
        return $this->summary()->productIds();
    }

    public function contains(int $productId): bool
    {
        return in_array($productId, $this->productIds(), true);
    }

    private function findWishlistableProduct(int $productId): Product
    {
        $product = Product::query()->find($productId);

        if (! $product || $product->trashed()) {
            throw new WishlistValidationException('This product is no longer available.');
        }

        if ($product->status !== ProductStatus::Published) {
            throw new WishlistValidationException('This product cannot be saved to your wishlist.');
        }

        return $product;
    }

    private function assertWishlistItemOwnership(WishlistItem $item): void
    {
        $wishlist = $this->resolve();

        if ($item->wishlist_id !== $wishlist->id) {
            abort(403);
        }
    }
}
