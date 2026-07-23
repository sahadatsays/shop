@php
    $reviews = $data['reviews'] ?? collect();
@endphp

@if ($reviews->isEmpty())
    <x-admin.empty-state title="No reviews" description="Product reviews will appear here." />
@else
    <ul class="divide-y admin-border">
        @foreach ($reviews as $review)
            <li class="py-3">
                <div class="flex items-center justify-between gap-2">
                    <p class="truncate text-sm font-medium admin-text">{{ $review['title'] ?: $review['product'] }}</p>
                    <span class="flex shrink-0 items-center gap-0.5 text-xs font-medium text-admin-warning">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.4 7.4H22l-6 4.6 2.3 7-6.3-4.6L5.7 21 8 14 2 9.4h7.6L12 2Z"/></svg>
                        {{ $review['rating'] }}/5
                    </span>
                </div>
                <p class="mt-0.5 text-xs admin-muted">
                    {{ $review['author'] }} · {{ $review['product'] }} · {{ $review['created'] }}
                    @if ($review['approved'])
                        <span class="text-admin-success">· Approved</span>
                    @else
                        <span class="text-admin-warning">· Pending</span>
                    @endif
                </p>
            </li>
        @endforeach
    </ul>
@endif
