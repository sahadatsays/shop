<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header :title="$offer->name" description="Offer details, linked products, and discount.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.offers.index')">Back</x-admin.button>
            @if (auth('admin')->user()?->hasPermission('offers.manage'))
                <x-admin.button size="sm" :href="route('admin.offers.edit', $offer)">Edit offer</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$offer->name" />
                    <x-admin.detail-row label="Slug">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $offer->slug }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Headline" :value="$offer->headline" />
                    <x-admin.detail-row label="Subheadline" :value="$offer->subheadline" />
                    <x-admin.detail-row label="Body" :value="$offer->body" />
                    <x-admin.detail-row label="CTA">
                        @if ($offer->cta_label)
                            {{ $offer->cta_label }} → {{ $offer->cta_url }}
                        @else
                            <span class="admin-muted">—</span>
                        @endif
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Discount" :value="$offer->discount?->name" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$offer->is_active ? 'success' : 'muted'">
                            {{ $offer->is_active ? 'Active' : 'Inactive' }}
                        </x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Sort order" :value="$offer->sort_order" />
                    <x-admin.detail-row label="Schedule">
                        @if ($offer->starts_at || $offer->ends_at)
                            {{ $offer->starts_at?->format('M j, Y g:i A') ?? 'Any time' }}
                            →
                            {{ $offer->ends_at?->format('M j, Y g:i A') ?? 'No end' }}
                        @else
                            <span class="admin-muted">Always on</span>
                        @endif
                    </x-admin.detail-row>
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Products" :description="$offer->products_count.' linked products'">
                @if ($offer->products->isEmpty())
                    <x-admin.empty-state title="No products linked" description="Edit this offer to add sale products." />
                @else
                    <div class="divide-y divide-admin-border/60">
                        @foreach ($offer->products as $product)
                            <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="font-medium admin-text">{{ $product->name }}</p>
                                    <p class="text-xs admin-muted">{{ $product->sku }}</p>
                                </div>
                                <div class="text-right text-sm">
                                    @if ($product->pivot->sale_price_cents)
                                        <p class="font-medium admin-text">{{ \App\Support\MoneyFormatter::format($product->pivot->sale_price_cents) }}</p>
                                    @else
                                        <p class="admin-muted">No override</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Image">
                @if ($offer->imageUrl())
                    <img src="{{ $offer->imageUrl() }}" alt="" class="aspect-[16/7] w-full rounded-[var(--radius-admin-lg)] object-cover ring-1 ring-black/5">
                @else
                    <div class="flex aspect-[16/7] items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-bg text-sm admin-muted">No image</div>
                @endif
            </x-admin.form-card>

            @if (auth('admin')->user()?->hasPermission('offers.manage'))
                <form method="POST" action="{{ route('admin.offers.destroy', $offer) }}" onsubmit="return confirm('Delete this offer?')">
                    @csrf
                    @method('DELETE')
                    <x-admin.button type="submit" variant="danger-ghost" class="w-full">Delete offer</x-admin.button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.admin>
