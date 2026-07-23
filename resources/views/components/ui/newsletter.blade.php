@props([
    'action' => null,
])

<section {{ $attributes->merge(['class' => 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8']) }} data-reveal data-newsletter>
    <div class="relative overflow-hidden rounded-card bg-linear-to-br from-olive-800 to-olive-600 px-6 py-16 shadow-card sm:px-12 lg:px-16">
        <div class="absolute -top-24 -right-24 size-72 rounded-full bg-bronze-400/20 blur-3xl" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-xl text-center">
            <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Join the ranks</h2>
            <p class="mt-4 text-lg text-olive-100">
                Early access to limited drops, exclusive offers, and stories from the veteran community.
            </p>
            <form class="mt-8 flex flex-col gap-3 sm:flex-row" method="post" action="{{ $action ?? route('newsletter.subscribe') }}" data-newsletter-form>
                @csrf
                <label for="newsletter-email" class="sr-only">Email address</label>
                <input type="email" id="newsletter-email" name="email" required placeholder="Enter your email"
                       class="w-full rounded-xl border-0 bg-white/95 px-5 py-3.5 text-sm text-ink placeholder:text-navy-400 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-300">
                <x-ui.button type="submit" variant="accent" class="shrink-0" data-newsletter-submit>
                    <span data-newsletter-label>Subscribe</span>
                </x-ui.button>
            </form>
            <p class="mt-4 text-sm text-olive-200" data-newsletter-status aria-live="polite">No spam. Unsubscribe anytime.</p>
        </div>
    </div>
</section>
