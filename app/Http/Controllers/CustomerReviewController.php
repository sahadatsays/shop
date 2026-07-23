<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductReviewRequest;
use App\Http\Requests\UpdateProductReviewRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use App\Services\ProductReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerReviewController extends Controller
{
    public function __construct(private ProductReviewService $reviews) {}

    public function index(): View
    {
        $customer = $this->customer();

        return view('account-reviews', [
            'reviews' => $this->reviews->forCustomer($customer),
            'reviewableProducts' => $this->reviews->reviewableProducts($customer),
            'stats' => $this->reviews->customerStats($customer),
            'openWriteProductId' => old('write_product_id'),
        ]);
    }

    public function store(StoreProductReviewRequest $request, Product $product): RedirectResponse
    {
        $this->reviews->create($this->customer(), $product, $request->validated());

        $redirect = $request->input('redirect') === 'account'
            ? route('account.reviews')
            : route('product.show', $product).'#reviews';

        return redirect()
            ->to($redirect)
            ->with('success', 'Thank you! Your review has been published.');
    }

    public function update(UpdateProductReviewRequest $request, Review $review): RedirectResponse
    {
        $this->reviews->update($this->customer(), $review, $request->validated());

        return redirect()
            ->route('account.reviews')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->reviews->delete($this->customer(), $review);

        return redirect()
            ->route('account.reviews')
            ->with('success', 'Review deleted.');
    }

    private function customer(): Customer
    {
        return Customer::query()->findOrFail(session('customer_id'));
    }
}
