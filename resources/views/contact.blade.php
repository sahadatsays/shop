@php
    $businessHours = [
        ['days' => 'Monday – Friday', 'hours' => '0800 – 1800 ET'],
        ['days' => 'Saturday', 'hours' => '1000 – 1400 ET'],
        ['days' => 'Sunday', 'hours' => 'Closed'],
    ];

    $faqPreview = [
        ['q' => 'How do I track my order?', 'a' => 'Sign in to Order History or use the tracking link in your shipping email. Visit our Order Tracking page with your order number and email.'],
        ['q' => 'What is your return policy?', 'a' => 'Returns accepted within 30 days on unworn items in original packaging. US orders receive a prepaid return label via Order History.'],
        ['q' => 'Do you offer a veteran discount?', 'a' => 'Verified veterans and active-duty members receive 10% off with ID.me verification at checkout.'],
        ['q' => 'How quickly will you respond?', 'a' => 'We reply to contact form messages within one business day. Live chat and phone support are available during business hours.'],
    ];

    $socialLinks = [
        ['name' => 'Instagram', 'href' => '#', 'path' => 'M8 3h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5Zm4 5.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7ZM17 6.8a.8.8 0 1 0 0 1.6.8.8 0 0 0 0-1.6Z'],
        ['name' => 'Facebook', 'href' => '#', 'path' => 'M14 9h3V6h-3a4 4 0 0 0-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2a1 1 0 0 1 1-1Z'],
        ['name' => 'YouTube', 'href' => '#', 'path' => 'M21 8s-.2-1.4-.8-2c-.8-.8-1.6-.8-2-.9C15.4 5 12 5 12 5s-3.4 0-6.2.1c-.4.1-1.2.1-2 .9-.6.6-.8 2-.8 2S3 9.6 3 11.2v1.5C3 14.4 3.2 16 3.2 16s.2 1.4.8 2c.8.8 1.8.8 2.2.9 1.6.1 5.8.1 5.8.1s3.4 0 6.2-.1c.4-.1 1.2-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.3v-1.5C21.2 9.6 21 8 21 8ZM10 14.6V9.4l5 2.6-5 2.6Z'],
    ];
@endphp

<x-layouts.app title="Contact Us" description="Get in touch with Valor Supply Co. — contact form, headquarters, business hours, and support information.">

    <div data-contact>

        {{-- Hero --}}
        <section class="relative overflow-hidden bg-navy-950">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(176,137,104,0.12),transparent_55%)]" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-300">
                        <li><a href="{{ route('home') }}" class="transition-colors duration-200 hover:text-white">Home</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="font-medium text-white">Contact</li>
                    </ol>
                </nav>
                <div class="mt-6 max-w-2xl">
                    <p class="text-sm font-semibold tracking-widest text-bronze-400 uppercase">We’re here to help</p>
                    <h1 class="mt-3 font-display text-4xl font-bold tracking-tight text-white sm:text-5xl">Contact us</h1>
                    <p class="mt-4 text-lg leading-relaxed text-navy-200">
                        Questions about an order, product, or partnership? Reach our veteran support team —
                        we typically respond within one business day.
                    </p>
                </div>
            </div>
        </section>

        {{-- Form + sidebar --}}
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-5 lg:gap-16">

                {{-- Contact form --}}
                <div class="lg:col-span-3">
                    <div class="rounded-card bg-surface p-7 shadow-soft sm:p-9">
                        <h2 class="font-display text-2xl font-bold text-navy-900">Send us a message</h2>
                        <p class="mt-2 text-sm text-navy-600">Fill out the form and we’ll get back to you shortly.</p>

                        <form class="mt-8 space-y-5" data-contact-form novalidate>
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <x-ui.input name="first-name" label="First name" autocomplete="given-name" required />
                                <x-ui.input name="last-name" label="Last name" autocomplete="family-name" required />
                            </div>
                            <x-ui.input name="email" type="email" label="Email address" autocomplete="email" placeholder="you@example.com" required />
                            <x-ui.input name="phone" type="tel" label="Phone (optional)" autocomplete="tel" placeholder="+1 (555) 000-0000" />
                            <div class="space-y-1.5">
                                <label for="contact-topic" class="block text-sm font-medium text-navy-900">Topic</label>
                                <select id="contact-topic" name="topic" required
                                        class="block w-full rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500">
                                    <option value="">Select a topic</option>
                                    <option value="order">Order & shipping</option>
                                    <option value="return">Returns & exchanges</option>
                                    <option value="product">Product question</option>
                                    <option value="wholesale">Wholesale & partnerships</option>
                                    <option value="press">Press & media</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label for="contact-message" class="block text-sm font-medium text-navy-900">Message</label>
                                <textarea id="contact-message" name="message" rows="5" required
                                          placeholder="How can we help?"
                                          class="block w-full resize-y rounded-field border border-navy-200 bg-surface px-4 py-3 text-sm leading-relaxed text-ink shadow-soft transition-colors duration-200 hover:border-navy-300 focus:outline-2 focus:outline-offset-2 focus:outline-bronze-500"></textarea>
                            </div>
                            <x-ui.button type="submit" variant="primary" class="w-full sm:w-auto" data-contact-submit>
                                <span data-contact-label>Send message</span>
                            </x-ui.button>
                            <p class="text-sm text-navy-500" data-contact-status aria-live="polite"></p>
                        </form>
                    </div>
                </div>

                {{-- Support sidebar --}}
                <aside class="space-y-6 lg:col-span-2">
                    {{-- Support information --}}
                    <div class="rounded-card bg-surface p-6 shadow-soft">
                        <h2 class="font-display text-lg font-bold text-navy-900">Support information</h2>
                        <ul class="mt-5 space-y-5">
                            <li class="flex gap-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-navy-900/5 text-navy-700">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-navy-900">Phone</p>
                                    <a href="tel:18008256726" class="mt-0.5 text-sm text-bronze-600 transition-colors duration-200 hover:text-bronze-700">1-800-VALOR-CO</a>
                                    <p class="mt-1 text-xs text-navy-500">Toll-free within the US</p>
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-navy-900/5 text-navy-700">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16v12H4zM4 7l8 6 8-6"/></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-navy-900">Email</p>
                                    <a href="mailto:support@valorsupply.co" class="mt-0.5 text-sm text-bronze-600 transition-colors duration-200 hover:text-bronze-700">support@valorsupply.co</a>
                                    <p class="mt-1 text-xs text-navy-500">Response within 1 business day</p>
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-navy-900/5 text-navy-700">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-navy-900">Headquarters</p>
                                    <address class="mt-0.5 not-italic text-sm leading-relaxed text-navy-600">
                                        1842 Veterans Parkway<br>
                                        Springfield, IL 62704<br>
                                        United States
                                    </address>
                                </div>
                            </li>
                        </ul>
                        <a href="{{ route('support') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-bronze-600 transition-colors duration-200 hover:text-bronze-700">
                            Visit help center
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </a>
                    </div>

                    {{-- Business hours --}}
                    <div class="rounded-card bg-surface p-6 shadow-soft">
                        <h2 class="font-display text-lg font-bold text-navy-900">Business hours</h2>
                        <p class="mt-1 text-xs text-navy-500">All times Eastern (ET)</p>
                        <ul class="mt-5 space-y-3">
                            @foreach ($businessHours as $row)
                                <li class="flex items-center justify-between gap-4 text-sm">
                                    <span class="font-medium text-navy-900">{{ $row['days'] }}</span>
                                    <span class="{{ $row['hours'] === 'Closed' ? 'text-navy-400' : 'text-navy-600' }}">{{ $row['hours'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-5 flex items-center gap-2 text-xs text-olive-700">
                            <span class="size-2 rounded-full bg-olive-500" aria-hidden="true"></span>
                            Live chat available during business hours
                        </p>
                    </div>

                    {{-- Social links --}}
                    <div class="rounded-card bg-surface p-6 shadow-soft">
                        <h2 class="font-display text-lg font-bold text-navy-900">Follow us</h2>
                        <p class="mt-1 text-sm text-navy-600">Field stories, new drops, and community updates.</p>
                        <div class="mt-5 flex gap-3">
                            @foreach ($socialLinks as $social)
                                <a href="{{ $social['href'] }}" aria-label="{{ $social['name'] }}"
                                   class="flex size-11 items-center justify-center rounded-xl bg-navy-900/5 text-navy-600 transition-all duration-200 hover:bg-navy-900 hover:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{{ $social['path'] }}"/></svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        {{-- Map --}}
        <section class="bg-canvas py-16 lg:py-20" aria-label="Office location map">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-card bg-surface shadow-soft">
                    <div class="grid grid-cols-1 lg:grid-cols-3">
                        <div class="relative min-h-64 lg:col-span-2 lg:min-h-96">
                            <svg viewBox="0 0 800 420" class="block size-full min-h-64" role="img" aria-label="Map showing Valor Supply Co. headquarters in Springfield, Illinois">
                                <rect width="800" height="420" class="fill-navy-50"/>
                                <rect x="80" y="60" width="140" height="100" rx="12" class="fill-olive-100"/>
                                <rect x="520" y="240" width="160" height="100" rx="12" class="fill-olive-100"/>
                                <path d="M0 320c100-40 160 30 260 0s180 40 280 16v84H0v-100Z" class="fill-navy-100"/>
                                <g class="stroke-white" stroke-width="14" stroke-linecap="round">
                                    <path d="M40 200h720"/>
                                    <path d="M220 40v340"/>
                                    <path d="M480 40v340"/>
                                    <path d="M40 100h720"/>
                                    <path d="M40 300h400"/>
                                </g>
                                <g class="stroke-navy-200/60" stroke-width="2" stroke-dasharray="8 10">
                                    <path d="M40 200h720"/>
                                    <path d="M220 40v340"/>
                                </g>
                                <path d="M400 120c-18 0-32 14-32 32 0 22 32 48 32 48s32-26 32-48c0-18-14-32-32-32Z" class="fill-bronze-500"/>
                                <circle cx="400" cy="152" r="10" class="fill-white"/>
                            </svg>
                            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-linear-to-t from-navy-900/75 to-transparent px-6 py-4">
                                <span class="text-sm font-medium text-white">Springfield, IL — Headquarters</span>
                                <span class="rounded-md bg-white/15 px-2.5 py-1 text-[0.65rem] font-semibold tracking-wide text-white uppercase backdrop-blur-sm">Maps API</span>
                            </div>
                        </div>
                        <div class="flex flex-col justify-center p-8 lg:p-10">
                            <x-ui.badge variant="navy" class="self-start">Visit by appointment</x-ui.badge>
                            <h2 class="mt-4 font-display text-xl font-bold text-navy-900">Valor Supply Co.</h2>
                            <address class="mt-3 not-italic text-sm leading-relaxed text-navy-600">
                                1842 Veterans Parkway<br>
                                Springfield, IL 62704
                            </address>
                            <p class="mt-4 text-sm text-navy-500">Our workshop and fulfillment center. Showroom visits available by appointment — email <a href="mailto:hello@valorsupply.co" class="font-medium text-bronze-600 hover:underline">hello@valorsupply.co</a>.</p>
                            <a href="https://maps.google.com/?q=Springfield+IL" target="_blank" rel="noopener noreferrer"
                               class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-bronze-600 transition-colors duration-200 hover:text-bronze-700">
                                Get directions
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M15 3h6v6M10 14 21 3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FAQ preview --}}
        <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8 lg:pb-28" aria-labelledby="faq-preview-heading">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold tracking-widest text-bronze-600 uppercase">Quick answers</p>
                    <h2 id="faq-preview-heading" class="mt-2 font-display text-3xl font-bold text-navy-900">FAQ preview</h2>
                    <p class="mt-2 text-navy-600">Common questions — browse the full help center for more.</p>
                </div>
                <x-ui.button :href="route('support') . '#faq'" variant="outline" size="sm">
                    View all FAQs
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </x-ui.button>
            </div>

            <div class="mt-8 rounded-card bg-surface px-6 shadow-soft sm:px-8">
                @foreach ($faqPreview as $index => $faq)
                    <x-ui.accordion-item :title="$faq['q']" :open="$index === 0" @class(['border-b-0' => $index === count($faqPreview) - 1])>
                        {{ $faq['a'] }}
                    </x-ui.accordion-item>
                @endforeach
            </div>
        </section>

    </div>

</x-layouts.app>
