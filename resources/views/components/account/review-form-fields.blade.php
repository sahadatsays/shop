@props([
    'ratingPickerId' => 'rating-picker',
    'titleId' => 'review-title',
    'bodyId' => 'review-body',
    'ratingValueName' => 'rating',
])

<fieldset>
    <legend class="text-sm font-medium text-navy-900">Your rating</legend>
    <div class="mt-2 flex gap-1" role="radiogroup" aria-label="Rating" data-rating-picker="{{ $ratingPickerId }}">
        @for ($i = 1; $i <= 5; $i++)
            <button type="button" data-rating-star="{{ $i }}" aria-label="Rate {{ $i }} out of 5 stars"
                    class="rounded-lg p-1 text-navy-200 transition-colors duration-150 hover:text-bronze-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bronze-500">
                <svg class="size-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z"/>
                </svg>
            </button>
        @endfor
    </div>
    <input type="hidden" name="rating" value="{{ old('rating', 5) }}" data-rating-value="{{ $ratingPickerId }}">
    @error('rating')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
</fieldset>

<div>
    <label for="{{ $titleId }}" class="block text-sm font-medium text-navy-900">Review title</label>
    <input type="text" id="{{ $titleId }}" name="title" value="{{ old('title') }}" maxlength="120"
           class="mt-1.5 block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
    @error('title')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="{{ $bodyId }}" class="block text-sm font-medium text-navy-900">Your review</label>
    <textarea id="{{ $bodyId }}" name="body" required rows="5" maxlength="2000"
              class="mt-1.5 block w-full resize-y rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm leading-relaxed text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">{{ old('body') }}</textarea>
    @error('body')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    @error('product')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    <p class="mt-1.5 text-xs text-navy-400">Share what you liked, how it fits, and how you use it.</p>
</div>
