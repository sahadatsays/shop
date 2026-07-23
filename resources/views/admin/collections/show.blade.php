<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header :title="$collection->name" description="Collection details and linked products.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.collections.index')">Back</x-admin.button>
            @if (auth('admin')->user()?->hasPermission('collections.manage'))
                <x-admin.button size="sm" :href="route('admin.collections.edit', $collection)">Edit collection</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$collection->name" />
                    <x-admin.detail-row label="Slug">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $collection->slug }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Description" :value="$collection->description" />
                    <x-admin.detail-row label="Featured">
                        @if ($collection->is_featured)
                            <x-admin.badge variant="brand">Featured</x-admin.badge>
                        @else
                            <span class="admin-muted">No</span>
                        @endif
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$collection->is_active ? 'success' : 'muted'">
                            {{ $collection->is_active ? 'Active' : 'Inactive' }}
                        </x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Sort order" :value="$collection->sort_order" />
                    <x-admin.detail-row label="Products" :value="$collection->products_count" />
                    <x-admin.detail-row label="Schedule">
                        @if ($collection->starts_at || $collection->ends_at)
                            {{ $collection->starts_at?->format('M j, Y g:i A') ?? 'Any time' }}
                            →
                            {{ $collection->ends_at?->format('M j, Y g:i A') ?? 'No end' }}
                        @else
                            <span class="admin-muted">Always on</span>
                        @endif
                    </x-admin.detail-row>
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Products">
                @if ($collection->products->isEmpty())
                    <x-admin.empty-state title="No products linked" description="Edit this collection to add products." />
                @else
                    <div class="divide-y divide-admin-border/60">
                        @foreach ($collection->products as $product)
                            <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="font-medium admin-text">{{ $product->name }}</p>
                                    <p class="text-xs admin-muted">{{ $product->sku }} · {{ $product->category?->name }}</p>
                                </div>
                                <p class="text-sm admin-text-secondary">{{ $product->formattedPrice() }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Cover image">
                @if ($collection->imageUrl())
                    <img src="{{ $collection->imageUrl() }}" alt="" class="aspect-square w-full max-w-xs rounded-[var(--radius-admin-lg)] object-cover ring-1 ring-black/5">
                @else
                    <div class="flex aspect-square max-w-xs items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-bg text-sm admin-muted">No image</div>
                @endif
            </x-admin.form-card>

            <x-admin.form-card title="Banner">
                @if ($collection->bannerUrl())
                    <img src="{{ $collection->bannerUrl() }}" alt="" class="aspect-[21/9] w-full rounded-[var(--radius-admin-lg)] object-cover ring-1 ring-black/5">
                @else
                    <div class="flex aspect-[21/9] items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-bg text-sm admin-muted">No banner</div>
                @endif
            </x-admin.form-card>

            @if (auth('admin')->user()?->hasPermission('collections.manage'))
                <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" onsubmit="return confirm('Delete this collection?')">
                    @csrf
                    @method('DELETE')
                    <x-admin.button type="submit" variant="danger-ghost" class="w-full">Delete collection</x-admin.button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.admin>
