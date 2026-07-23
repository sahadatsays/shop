@php
    $offer ??= null;
    $formatDatetimeLocal = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';
    $productRows = old('products', isset($offer)
        ? $offer->products->map(fn ($product) => [
            'product_id' => $product->id,
            'sale_price' => $product->pivot->sale_price_cents
                ? number_format($product->pivot->sale_price_cents / 100, 2, '.', '')
                : '',
        ])->values()->all()
        : [['product_id' => '', 'sale_price' => '']]);

    if (empty($productRows)) {
        $productRows = [['product_id' => '', 'sale_price' => '']];
    }
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="categoryForm(@js(['imageUrl' => $offer?->imageUrl()]))"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Offer content" description="Headlines and call-to-action for storefront placements.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Name"
                        name="name"
                        :value="old('name', $offer?->name ?? '')"
                        placeholder="Spring Field Gear Event"
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.input
                        label="Slug"
                        name="slug"
                        :value="old('slug', $offer?->slug ?? '')"
                        placeholder="spring-field-gear"
                        help="Leave blank to auto-generate from name."
                        class="font-mono sm:col-span-2"
                    />

                    <x-admin.input
                        label="Headline"
                        name="headline"
                        :value="old('headline', $offer?->headline ?? '')"
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.input
                        label="Subheadline"
                        name="subheadline"
                        :value="old('subheadline', $offer?->subheadline ?? '')"
                        class="sm:col-span-2"
                    />

                    <x-admin.textarea
                        label="Body"
                        name="body"
                        rows="4"
                        class="sm:col-span-2"
                    >{{ old('body', $offer?->body ?? '') }}</x-admin.textarea>

                    <x-admin.input label="CTA label" name="cta_label" :value="old('cta_label', $offer?->cta_label ?? '')" placeholder="Shop the event" />
                    <x-admin.input label="CTA URL" name="cta_url" :value="old('cta_url', $offer?->cta_url ?? '')" placeholder="/shop?on_sale=1" />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Products" description="Link products with optional event sale prices.">
                <div
                    class="space-y-3"
                    x-data="{
                        rows: @js($productRows),
                        addRow() { this.rows.push({ product_id: '', sale_price: '' }); },
                        removeRow(index) { this.rows.splice(index, 1); if (! this.rows.length) this.addRow(); },
                    }"
                >
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="grid gap-3 rounded-[var(--radius-admin)] border admin-border bg-admin-bg/40 p-3 sm:grid-cols-[1fr_140px_auto]">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium admin-text">Product</label>
                                <select
                                    :name="`products[${index}][product_id]`"
                                    class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text admin-focus-ring"
                                    x-model="row.product_id"
                                >
                                    <option value="">Select product…</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium admin-text">Sale price</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :name="`products[${index}][sale_price]`"
                                    x-model="row.sale_price"
                                    placeholder="0.00"
                                    class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 text-sm admin-text admin-focus-ring"
                                >
                            </div>

                            <div class="flex items-end">
                                <button type="button" @click="removeRow(index)" class="rounded-[var(--radius-admin)] border admin-border px-3 py-2.5 text-sm admin-muted transition-colors hover:bg-admin-bg hover:admin-text admin-focus-ring">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>
                    <x-admin.button type="button" variant="secondary" size="sm" @click="addRow">Add product</x-admin.button>
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Schedule">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Starts at"
                        name="starts_at"
                        type="datetime-local"
                        :value="old('starts_at', $formatDatetimeLocal($offer?->starts_at))"
                    />
                    <x-admin.input
                        label="Ends at"
                        name="ends_at"
                        type="datetime-local"
                        :value="old('ends_at', $formatDatetimeLocal($offer?->ends_at))"
                    />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Settings">
                <div class="space-y-5">
                    <x-admin.select label="Linked discount" name="discount_id" help="Optional checkout discount for this event.">
                        <option value="">None</option>
                        @foreach ($discounts as $discount)
                            <option value="{{ $discount->id }}" @selected((int) old('discount_id', $offer?->discount_id ?? 0) === $discount->id)>
                                {{ $discount->code }} — {{ $discount->name }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Sort order"
                        name="sort_order"
                        type="number"
                        min="0"
                        :value="old('sort_order', $offer?->sort_order ?? 0)"
                    />

                    <x-admin.checkbox
                        label="Active"
                        name="is_active"
                        :checked="(bool) old('is_active', $offer?->is_active ?? true)"
                    />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Image">
                <x-admin.image-upload
                    label="Offer image"
                    name="image"
                    preview="imagePreview"
                    :current="$offer?->imageUrl()"
                    aspect="wide"
                    help="Wide hero image. PNG or WEBP up to 4MB."
                />
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="$cancelRoute" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
