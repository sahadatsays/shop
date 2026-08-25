@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.adminToast?.push({
                title: @json(session('success')),
                type: 'success'
            });
        });
    </script>
@endif

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20"
        role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Section visibility"
                description="Choose which homepage sections are shown on the storefront.">
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($sectionLabels as $key => $label)
                        <x-admin.checkbox :label="$label" name="enabled_sections[]" :value="$key"
                            :checked="in_array($key, old('enabled_sections', $enabledSections), true)" />
                    @endforeach
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Section limits" description="Maximum items shown per dynamic section.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input label="Featured products" name="featured_products_limit" type="number"
                        min="1" max="24" :value="old('featured_products_limit', $settings->featured_products_limit)" required />
                    <x-admin.input label="New arrivals" name="new_arrivals_limit" type="number" min="1"
                        max="24" :value="old('new_arrivals_limit', $settings->new_arrivals_limit)" required />
                    <x-admin.input label="Best sellers" name="best_sellers_limit" type="number" min="1"
                        max="24" :value="old('best_sellers_limit', $settings->best_sellers_limit)" required />
                    <x-admin.input label="Brands" name="brands_limit" type="number" min="1" max="24"
                        :value="old('brands_limit', $settings->brands_limit)" required />
                    <x-admin.input label="Categories" name="categories_limit" type="number" min="1"
                        max="24" :value="old('categories_limit', $settings->categories_limit)" required />
                    <x-admin.input label="Reviews" name="reviews_limit" type="number" min="1" max="24"
                        :value="old('reviews_limit', $settings->reviews_limit)" required />
                    <x-admin.input label="New badge days" name="new_badge_days" type="number" min="1"
                        max="365" :value="old('new_badge_days', $settings->new_badge_days)" required class="sm:col-span-2" />
                    <x-admin.checkbox label="Hide out-of-stock products" name="hide_out_of_stock" :checked="(bool) old('hide_out_of_stock', $settings->hide_out_of_stock)"
                        class="sm:col-span-2" />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="SEO" description="Homepage meta tags for search engines.">
                <div class="space-y-5">
                    <x-admin.input label="Meta title" name="meta_title" :value="old('meta_title', $settings->meta_title)" />
                    <x-admin.textarea label="Meta description" name="meta_description"
                        rows="3">{{ old('meta_description', $settings->meta_description) }}</x-admin.textarea>
                    <x-admin.input label="Meta keywords" name="meta_keywords" :value="old('meta_keywords', $settings->meta_keywords)" />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Search" description="Popular search suggestions shown on the search page.">
                <x-admin.textarea label="Popular searches" name="popular_searches" rows="6"
                    help="Comma-separated keywords.">{{ old('popular_searches', implode(', ', $settings->popular_searches ?? [])) }}</x-admin.textarea>
            </x-admin.form-card>

            @if (auth('admin')->user()?->hasPermission('homepage.manage'))
                <x-admin.button type="submit" class="w-full">Save settings</x-admin.button>
            @endif
        </div>
    </div>
</form>
