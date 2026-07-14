@php
    $contactOptions = [
        [
            'id' => 'live-chat',
            'label' => 'Live Chat',
            'desc' => 'Chat with our veteran support team. Typical reply under 2 minutes.',
            'meta' => 'Online now',
            'metaVariant' => 'olive',
            'icon' => 'M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.4-4 8-9 8a9.8 9.8 0 0 1-4-.8L3 21l1.8-4.2A8.8 8.8 0 0 1 3 12c0-4.4 4-8 9-8s9 3.6 9 8Z',
            'action' => 'Start chat',
            'trigger' => 'chat',
        ],
        [
            'id' => 'call',
            'label' => 'Call Support',
            'desc' => 'Speak directly with a specialist. Mon–Fri, 0800–1800 ET.',
            'meta' => '1-800-VALOR-CO',
            'metaVariant' => 'navy',
            'icon' => 'M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z',
            'action' => 'Call now',
            'href' => 'tel:18008256726',
        ],
        [
            'id' => 'email',
            'label' => 'Email Support',
            'desc' => 'Send a detailed message. We respond within one business day.',
            'meta' => 'support@valorsupply.co',
            'metaVariant' => 'bronze',
            'icon' => 'M4 6h16v12H4zM4 7l8 6 8-6',
            'action' => 'Send email',
            'href' => 'mailto:support@valorsupply.co',
        ],
        [
            'id' => 'ticket',
            'label' => 'Support Tickets',
            'desc' => 'Open a tracked request for orders, returns, or account issues.',
            'meta' => '2 open tickets',
            'metaVariant' => 'navy',
            'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5h6',
            'action' => 'View tickets',
            'trigger' => 'tickets',
        ],
    ];

    $knowledgeBase = [
        ['category' => 'Orders & Shipping', 'articles' => 12, 'icon' => 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7ZM9 10V6a3 3 0 0 1 6 0v4', 'topics' => ['Track an order', 'Shipping times', 'Express delivery']],
        ['category' => 'Returns & Exchanges', 'articles' => 8, 'icon' => 'M3 7h11v8H3zM14 10h4l3 3v2h-7zM7 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm11 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z', 'topics' => ['Return policy', 'Start a return', 'Exchange sizing']],
        ['category' => 'Products & Sizing', 'articles' => 15, 'icon' => 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z', 'topics' => ['Size guides', 'Care instructions', 'Warranty coverage']],
        ['category' => 'Account & Payments', 'articles' => 10, 'icon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5-3a7.5 7.5 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7.6 7.6 0 0 0-2-1.2L14.6 3h-4l-.4 2.6a7.6 7.6 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.7 7.7 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7.6 7.6 0 0 0 2 1.2l.4 2.6h4l.4-2.6a7.6 7.6 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.06-.4.1-.8.1-1.2Z', 'topics' => ['Reset password', 'Payment methods', 'Rewards program']],
        ['category' => 'Veteran Programs', 'articles' => 6, 'icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z', 'topics' => ['Military discount', 'Giving back', 'Wholesale inquiries']],
        ['category' => 'Gift Cards', 'articles' => 4, 'icon' => 'M4 7h16v10H4zM4 11h16M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2', 'topics' => ['Redeem a gift card', 'Check balance', 'Purchase gift cards']],
    ];

    $faqs = [
        ['category' => 'orders', 'q' => 'How do I track my order?', 'a' => 'Sign in to your account and open Order History, or use the tracking link in your shipping confirmation email. You can also visit our Order Tracking page and enter your order number and email.'],
        ['category' => 'orders', 'q' => 'When will my order arrive?', 'a' => 'Standard shipping takes 5–7 business days. Express shipping (orders over $75) arrives in 2–3 business days. You\'ll receive tracking details as soon as your order ships.'],
        ['category' => 'returns', 'q' => 'What is your return policy?', 'a' => 'We accept returns within 30 days of delivery on unworn, unused items in original packaging. Field gear must be returned with all tags attached. Refunds are processed within 5–7 business days after we receive your return.'],
        ['category' => 'returns', 'q' => 'How do I start a return or exchange?', 'a' => 'Go to Order History, select the order, and click "Start a return." Choose the items and reason — we\'ll email a prepaid return label for US orders. Exchanges ship as soon as we receive your return.'],
        ['category' => 'products', 'q' => 'How do I find the right size?', 'a' => 'Each product page includes a size guide link. Apparel runs true to size with a relaxed fit. When in doubt, size up for layering. Our support team can help with specific measurements.'],
        ['category' => 'account', 'q' => 'Do you offer a military or veteran discount?', 'a' => 'Yes. Verified veterans and active-duty service members receive 10% off with ID verification at checkout. The discount stacks with member rewards but not with other promo codes.'],
        ['category' => 'account', 'q' => 'How do Valor rewards points work?', 'a' => 'Earn 1 point per dollar spent. Gold members earn 1.5×. Redeem 100 points for $5 off. Points appear in your account within 48 hours of delivery and never expire for active members.'],
        ['category' => 'payments', 'q' => 'What payment methods do you accept?', 'a' => 'We accept Visa, Mastercard, Amex, PayPal, Apple Pay, Google Pay, and Valor gift cards. Split payment between a gift card and card is supported at checkout.'],
    ];

    $faqFilters = ['All', 'Orders', 'Returns', 'Products', 'Account', 'Payments'];

    $tickets = [
        ['id' => 'TKT-4829', 'subject' => 'Order #VS-10482 — delivery question', 'status' => 'Open', 'statusVariant' => 'bronze', 'updated' => 'Jul 13, 2026', 'category' => 'Orders'],
        ['id' => 'TKT-4712', 'subject' => 'Size exchange — Ranger Field Jacket', 'status' => 'Resolved', 'statusVariant' => 'olive', 'updated' => 'Jun 28, 2026', 'category' => 'Returns'],
    ];

    $searchArticles = collect($knowledgeBase)->flatMap(fn ($kb) => collect($kb['topics'])->map(fn ($t) => ['title' => $t, 'category' => $kb['category']]))->merge(collect($faqs)->map(fn ($f) => ['title' => $f['q'], 'category' => ucfirst($f['category'])]))->values();
@endphp

<x-layouts.app title="Help Center" description="Valor Supply Co. customer support — search help articles, browse FAQs, open tickets, live chat, call, or email our team.">

    <div data-support>

        {{-- Hero + search --}}
        <section class="relative overflow-hidden bg-navy-950">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(176,137,104,0.12),transparent_60%)]" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-3xl px-4 py-20 text-center sm:px-6 lg:py-28">
                <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">Help center</p>
                <h1 class="mt-4 font-display text-4xl font-bold tracking-tight text-white sm:text-5xl">How can we help?</h1>
                <p class="mt-4 text-lg text-navy-200">Search guides, browse FAQs, or reach our veteran support team directly.</p>

                <form class="relative mt-10" data-support-search-form role="search">
                    <label for="support-search" class="sr-only">Search help articles</label>
                    <svg class="pointer-events-none absolute top-1/2 left-5 size-5 -translate-y-1/2 text-navy-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                    </svg>
                    <input type="search" id="support-search" name="q" data-support-search-input
                           placeholder="Search orders, returns, sizing, rewards…"
                           autocomplete="off"
                           class="w-full rounded-card border-0 bg-white py-4 pr-28 pl-14 text-base text-ink shadow-glass placeholder:text-navy-400 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                    <button type="submit"
                            class="absolute top-1/2 right-2 -translate-y-1/2 rounded-xl bg-navy-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-navy-800">
                        Search
                    </button>
                </form>

                {{-- Search results dropdown --}}
                <div data-support-search-results hidden
                     class="mt-2 overflow-hidden rounded-card bg-surface text-left shadow-glass ring-1 ring-navy-900/5">
                    <ul class="max-h-64 divide-y divide-navy-100 overflow-y-auto" data-support-search-list></ul>
                    <p data-support-search-empty hidden class="px-5 py-4 text-sm text-navy-500">No articles found. Try different keywords or contact support.</p>
                </div>

                @foreach ($searchArticles as $article)
                    <span hidden data-search-item data-search-title="{{ strtolower($article['title']) }}" data-search-category="{{ $article['category'] }}">{{ $article['title'] }}</span>
                @endforeach
            </div>
        </section>

        {{-- Contact options --}}
        <section id="contact" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20" aria-labelledby="contact-heading">
            <x-ui.section-heading align="left" eyebrow="Get in touch" title="Choose how to reach us" subtitle="Real people, veteran-owned support — pick the channel that works best for you." />

            <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($contactOptions as $option)
                    <article class="flex flex-col rounded-card bg-surface p-6 shadow-soft transition-all duration-300 ease-out hover:-translate-y-0.5 hover:shadow-card">
                        <span class="flex size-12 items-center justify-center rounded-xl bg-navy-900/5 text-navy-700">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $option['icon'] }}"/>
                            </svg>
                        </span>
                        <h2 class="mt-4 font-display text-lg font-bold text-navy-900">{{ $option['label'] }}</h2>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-navy-600">{{ $option['desc'] }}</p>
                        <p class="mt-3 text-xs font-semibold {{ match($option['metaVariant']) {
                            'olive' => 'text-olive-600',
                            'bronze' => 'text-bronze-600',
                            default => 'text-navy-600',
                        } }}">{{ $option['meta'] }}</p>

                        @if (isset($option['href']))
                            <a href="{{ $option['href'] }}"
                               class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-bronze-600 transition-colors duration-200 hover:text-bronze-700">
                                {{ $option['action'] }}
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </a>
                        @else
                            <button type="button" data-support-trigger="{{ $option['trigger'] }}"
                                    class="mt-5 inline-flex items-center gap-2 text-left text-sm font-semibold text-bronze-600 transition-colors duration-200 hover:text-bronze-700">
                                {{ $option['action'] }}
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </button>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Knowledge base --}}
        <section id="knowledge-base" class="bg-canvas py-16 lg:py-20" aria-labelledby="kb-heading">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-ui.section-heading align="left" eyebrow="Knowledge base" title="Browse by topic" subtitle="Step-by-step guides written by our team — updated regularly." />

                <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($knowledgeBase as $kb)
                        <article class="group rounded-card bg-surface p-6 shadow-soft transition-all duration-300 ease-out hover:-translate-y-0.5 hover:shadow-card">
                            <div class="flex items-start justify-between gap-3">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-olive-100 text-olive-700 transition-colors duration-200 group-hover:bg-olive-600 group-hover:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="{{ $kb['icon'] }}"/>
                                    </svg>
                                </span>
                                <span class="text-xs font-medium text-navy-400">{{ $kb['articles'] }} articles</span>
                            </div>
                            <h2 class="mt-4 font-display text-lg font-bold text-navy-900">{{ $kb['category'] }}</h2>
                            <ul class="mt-3 space-y-2">
                                @foreach ($kb['topics'] as $topic)
                                    <li>
                                        <a href="#" class="text-sm text-navy-600 underline-offset-4 transition-colors duration-200 hover:text-navy-900 hover:underline">{{ $topic }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="#" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-bronze-600 transition-colors duration-200 hover:text-bronze-700">
                                View all
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section id="faq" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20" aria-labelledby="faq-heading">
            <x-ui.section-heading align="left" eyebrow="FAQ" title="Frequently asked questions" subtitle="Quick answers to the most common questions from our community." />

            <div class="mt-8 flex flex-wrap gap-2" role="group" aria-label="Filter FAQ by topic">
                @foreach ($faqFilters as $filter)
                    <button type="button" data-faq-filter="{{ strtolower($filter) }}"
                            aria-pressed="{{ $filter === 'All' ? 'true' : 'false' }}"
                            class="rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 {{ $filter === 'All' ? 'bg-navy-900 text-white shadow-soft' : 'bg-surface text-navy-700 shadow-soft hover:bg-navy-900/5' }}">
                        {{ $filter }}
                    </button>
                @endforeach
            </div>

            <div class="mt-8 rounded-card bg-surface px-6 shadow-soft sm:px-8" data-faq-list>
                @foreach ($faqs as $index => $faq)
                    <x-ui.accordion-item :title="$faq['q']" :open="$index === 0" data-faq-item data-faq-category="{{ $faq['category'] }}" @class(['border-b-0' => $index === count($faqs) - 1])>
                        {{ $faq['a'] }}
                    </x-ui.accordion-item>
                @endforeach
            </div>

            <p data-faq-empty hidden class="mt-8 text-center text-sm text-navy-500">No FAQs in this category.</p>
        </section>

        {{-- Tickets --}}
        <section id="tickets" class="bg-canvas py-16 lg:py-20" aria-labelledby="tickets-heading">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-5">
                    <div class="lg:col-span-2">
                        <x-ui.section-heading align="left" eyebrow="Support tickets" title="Your requests" subtitle="Track open issues or submit a new ticket — we respond within 24 hours." />

                        <div class="mt-8 space-y-4" data-tickets-list>
                            @foreach ($tickets as $ticket)
                                <article data-ticket-card class="rounded-card bg-surface p-5 shadow-soft">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <p class="font-mono text-xs text-navy-400">{{ $ticket['id'] }}</p>
                                        <x-ui.badge :variant="$ticket['statusVariant']">{{ $ticket['status'] }}</x-ui.badge>
                                    </div>
                                    <h3 class="mt-2 font-display text-base font-semibold text-navy-900">{{ $ticket['subject'] }}</h3>
                                    <p class="mt-1 text-xs text-navy-500">{{ $ticket['category'] }} · Updated {{ $ticket['updated'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="lg:col-span-3">
                        <div class="rounded-card bg-surface p-7 shadow-soft lg:p-8">
                            <h2 class="font-display text-xl font-bold text-navy-900">Open a new ticket</h2>
                            <p class="mt-1 text-sm text-navy-500">Describe your issue and we’ll assign a specialist to help.</p>

                            <form class="mt-6 space-y-4" data-ticket-form novalidate>
                                <x-ui.input name="ticket-subject" label="Subject" placeholder="Brief summary of your issue" required />
                                <div class="space-y-1.5">
                                    <label for="ticket-category" class="block text-sm font-medium text-navy-900">Category</label>
                                    <select id="ticket-category" name="ticket-category" required
                                            class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                        <option value="">Select a category</option>
                                        <option value="Orders">Orders & shipping</option>
                                        <option value="Returns">Returns & exchanges</option>
                                        <option value="Products">Product question</option>
                                        <option value="Account">Account & billing</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="ticket-message" class="block text-sm font-medium text-navy-900">Message</label>
                                    <textarea id="ticket-message" name="ticket-message" rows="5" required
                                              placeholder="Include your order number if relevant…"
                                              class="block w-full resize-y rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm leading-relaxed text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500"></textarea>
                                </div>
                                <x-ui.button type="submit" variant="primary" data-ticket-submit>
                                    <span data-ticket-label>Submit ticket</span>
                                </x-ui.button>
                                <p class="text-sm text-navy-500" data-ticket-status aria-live="polite"></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Live chat panel --}}
        <div data-support-chat hidden
             class="fixed inset-x-4 bottom-4 z-50 flex max-h-[32rem] flex-col overflow-hidden rounded-card bg-surface shadow-glass sm:inset-x-auto sm:right-6 sm:bottom-6 sm:w-96"
             role="dialog" aria-labelledby="chat-heading" aria-modal="true">
            <div class="flex items-center justify-between gap-3 border-b border-navy-100 bg-navy-900 px-5 py-4 text-white">
                <div>
                    <h2 id="chat-heading" class="font-display text-base font-bold">Live chat</h2>
                    <p class="flex items-center gap-1.5 text-xs text-navy-200">
                        <span class="size-2 rounded-full bg-olive-400" aria-hidden="true"></span>
                        Valor Support · Typically replies instantly
                    </p>
                </div>
                <button type="button" data-support-chat-close aria-label="Close chat"
                        class="flex size-9 items-center justify-center rounded-xl text-navy-300 transition-colors duration-200 hover:bg-white/10 hover:text-white">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 space-y-4 overflow-y-auto p-5" data-chat-messages aria-live="polite">
                <div class="flex gap-3">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-olive-100 text-xs font-bold text-olive-700">VS</span>
                    <div class="rounded-xl rounded-tl-none bg-canvas px-4 py-3 text-sm text-navy-700">
                        Welcome to Valor Support. How can we help you today?
                    </div>
                </div>
            </div>

            <form class="border-t border-navy-100 p-4" data-chat-form>
                <div class="flex gap-2">
                    <label for="chat-input" class="sr-only">Type a message</label>
                    <input type="text" id="chat-input" data-chat-input placeholder="Type your message…" autocomplete="off"
                           class="min-w-0 flex-1 rounded-field border border-navy-200 bg-surface px-4 py-2.5 text-sm text-ink shadow-soft focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                    <button type="submit" aria-label="Send message"
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-white transition-colors duration-200 hover:bg-navy-800">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>

</x-layouts.app>
