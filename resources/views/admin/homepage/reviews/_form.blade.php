@php $review ??= null; @endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf @if ($method !== 'POST') @method($method) @endif
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Review">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input label="Author name" name="author_name" :value="old('author_name', $review?->author_name)" required class="sm:col-span-2" />
                    <x-admin.select label="Product" name="product_id" class="sm:col-span-2">
                        <option value="">No product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id', $review?->product_id) == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </x-admin.select>
                    <x-admin.input label="Rating" name="rating" type="number" min="1" max="5" :value="old('rating', $review?->rating ?? 5)" required />
                    <x-admin.input label="Title" name="title" :value="old('title', $review?->title)" />
                    <x-admin.textarea label="Review body" name="body" rows="5" required class="sm:col-span-2">{{ old('body', $review?->body) }}</x-admin.textarea>
                    <x-admin.checkbox label="Approved for homepage" name="is_approved" :checked="(bool) old('is_approved', $review?->is_approved ?? false)" class="sm:col-span-2" />
                </div>
            </x-admin.form-card>
        </div>
        <div class="flex flex-col gap-2">
            <x-admin.button type="submit" class="w-full">{{ $submitLabel }}</x-admin.button>
            <x-admin.button variant="secondary" :href="route('admin.homepage.reviews.index')" class="w-full">Cancel</x-admin.button>
        </div>
    </div>
</form>
