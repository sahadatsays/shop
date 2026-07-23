@php
    $formatDatetimeLocal = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';
    $endsAtRequired = $endsAtRequired ?? false;
    $promotion ??= null;
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
    x-data="categoryForm(@js([
        'name' => old('name', $promotion?->name ?? ''),
        'slug' => old('slug', $promotion?->slug ?? ''),
        'autoSlug' => ! $promotion && ! old('slug'),
        'imageUrl' => $promotion?->imageUrl(),
    ]))"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Promotion content">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Name"
                        name="name"
                        x-model="name"
                        @input="onNameInput"
                        :value="old('name', $promotion?->name ?? '')"
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.field label="Slug" name="slug" help="Leave blank to auto-generate from name." class="sm:col-span-2">
                        <input
                            type="text"
                            name="slug"
                            id="field-slug"
                            x-model="slug"
                            @input="onSlugInput"
                            value="{{ old('slug', $promotion?->slug ?? '') }}"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3.5 py-2.5 font-mono text-sm admin-text admin-focus-ring"
                        >
                    </x-admin.field>

                    <x-admin.select label="Placement" name="placement" required class="sm:col-span-2">
                        @foreach (\App\Enums\PromotionPlacement::cases() as $placement)
                            <option value="{{ $placement->value }}" @selected(old('placement', $promotion?->placement?->value) === $placement->value)>
                                {{ $placement->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Headline"
                        name="headline"
                        :value="old('headline', $promotion?->headline ?? '')"
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.input
                        label="Subheadline"
                        name="subheadline"
                        :value="old('subheadline', $promotion?->subheadline ?? '')"
                        class="sm:col-span-2"
                    />

                    <x-admin.textarea
                        label="Body"
                        name="body"
                        rows="4"
                        class="sm:col-span-2"
                    >{{ old('body', $promotion?->body ?? '') }}</x-admin.textarea>

                    <x-admin.input label="CTA label" name="cta_label" :value="old('cta_label', $promotion?->cta_label ?? '')" />
                    <x-admin.input label="CTA URL" name="cta_url" :value="old('cta_url', $promotion?->cta_url ?? '')" />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Schedule">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Starts at"
                        name="starts_at"
                        type="datetime-local"
                        :value="old('starts_at', $formatDatetimeLocal($promotion?->starts_at ?? null))"
                    />
                    <x-admin.input
                        label="Ends at"
                        name="ends_at"
                        type="datetime-local"
                        :value="old('ends_at', $formatDatetimeLocal($promotion?->ends_at ?? null))"
                        :required="$endsAtRequired"
                        :help="$endsAtRequired ? 'Required for countdown promotions.' : null"
                    />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Links & settings">
                <div class="space-y-5">
                    <x-admin.select label="Collection" name="collection_id">
                        <option value="">None</option>
                        @foreach ($collections as $collection)
                            <option value="{{ $collection->id }}" @selected((int) old('collection_id', $promotion?->collection_id ?? 0) === $collection->id)>
                                {{ $collection->name }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.select label="Offer" name="offer_id">
                        <option value="">None</option>
                        @foreach ($offers as $offer)
                            <option value="{{ $offer->id }}" @selected((int) old('offer_id', $promotion?->offer_id ?? 0) === $offer->id)>
                                {{ $offer->name }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Sort order"
                        name="sort_order"
                        type="number"
                        min="0"
                        :value="old('sort_order', $promotion?->sort_order ?? 0)"
                    />

                    <x-admin.checkbox
                        label="Active"
                        name="is_active"
                        :checked="(bool) old('is_active', $promotion?->is_active ?? true)"
                    />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Image">
                <x-admin.image-upload
                    label="Promotion image"
                    name="image"
                    preview="imagePreview"
                    :current="$promotion?->imageUrl() ?? null"
                    aspect="wide"
                    help="Hero or banner image. Max 4MB."
                />
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="$cancelRoute" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
