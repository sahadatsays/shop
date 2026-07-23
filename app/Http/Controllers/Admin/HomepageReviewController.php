<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomepageReviewRequest;
use App\Models\Review;
use App\Services\Admin\HomepageReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomepageReviewController extends Controller
{
    public function __construct(private HomepageReviewService $reviews) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        return view('admin.homepage.reviews.index', [
            'title' => 'Homepage Reviews',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Homepage Reviews'],
            ],
            'reviews' => $this->reviews->list([
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ]),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.reviews.create', [
            'title' => 'Create Review',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Homepage Reviews', 'href' => route('admin.homepage.reviews.index')],
                ['label' => 'Create'],
            ],
            'products' => $this->reviews->productOptions(),
        ]);
    }

    public function store(HomepageReviewRequest $request): RedirectResponse
    {
        $this->reviews->create($request->validated());

        return redirect()
            ->route('admin.homepage.reviews.index')
            ->with('success', 'Review created successfully.');
    }

    public function edit(Review $review): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        return view('admin.homepage.reviews.edit', [
            'title' => 'Edit Review',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Homepage Reviews', 'href' => route('admin.homepage.reviews.index')],
                ['label' => $review->author_name],
            ],
            'review' => $review,
            'products' => $this->reviews->productOptions(),
        ]);
    }

    public function update(HomepageReviewRequest $request, Review $review): RedirectResponse
    {
        $this->reviews->update($review, $request->validated());

        return redirect()
            ->route('admin.homepage.reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.manage'), 403);

        $this->reviews->delete($review);

        return redirect()
            ->route('admin.homepage.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
