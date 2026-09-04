@php
    $themeColors = old('theme_colors', $settings->themeColors());
    $social = $settings->socialLinks();
    $moneyField = static function (?int $cents, string $oldKey): string {
        if (old($oldKey) !== null) {
            return (string) old($oldKey);
        }

        return $cents !== null
            ? number_format($cents / 100, 2, '.', '')
            : '';
    };
    $freeShipping = $moneyField($settings->free_shipping_threshold_cents, 'free_shipping_threshold');
    $flatShipping = $moneyField($settings->flat_shipping_cents, 'flat_shipping');
    $insideDhakaShipping = $moneyField($settings->inside_dhaka_shipping_cents, 'inside_dhaka_shipping');
    $outsideDhakaShipping = $moneyField($settings->outside_dhaka_shipping_cents, 'outside_dhaka_shipping');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" x-data="{ tab: 'general' }" class="space-y-6">
    @csrf
    @method($method)

    <div class="overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border admin-surface shadow-sm">
        <div class="border-b admin-border px-4 sm:px-6">
            <div class="flex gap-1 overflow-x-auto admin-scrollbar" role="tablist">
                @foreach ([
        'general' => 'General',
        'contact' => 'Contact',
        'regional' => 'Regional',
        'email' => 'Email',
        'maintenance' => 'Maintenance',
        'seo' => 'SEO',
        'appearance' => 'Appearance',
    ] as $id => $label)
                    <button type="button" role="tab" @click="tab = '{{ $id }}'"
                        :aria-selected="tab === '{{ $id }}'"
                        :class="tab === '{{ $id }}'
                            ?
                            'border-admin-accent admin-text' :
                            'border-transparent admin-muted hover:admin-text-secondary'"
                        class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium admin-focus-ring">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="space-y-6 p-4 sm:p-6">
            {{-- General --}}
            <div x-show="tab === 'general'" x-cloak class="grid gap-6 lg:grid-cols-2">
                <x-admin.form-card title="Store information" class="lg:col-span-2">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.input label="Store name" name="store_name" :value="old('store_name', $settings->store_name)" required />
                        <x-admin.input label="Tagline / slogan" name="tagline" :value="old('tagline', $settings->tagline)"
                            placeholder="Honor in every stitch and seam." />
                    </div>
                    <div class="mt-5">
                        <x-admin.textarea label="Store description" name="description" rows="4"
                            :value="old('description', $settings->description)" />
                    </div>
                    <div class="mt-5">
                        <x-admin.input label="Utility bar message" name="utility_bar_message" :value="old('utility_bar_message', $settings->utility_bar_message)"
                            help="Shown in the top announcement bar across the storefront." />
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Shipping rates" class="lg:col-span-2">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.input label="Free shipping threshold (BDT)" name="free_shipping_threshold"
                            type="number" step="0.01" min="0" :value="$freeShipping" placeholder="2000.00"
                            help="Used for flat-rate estimates before a delivery zone is chosen." />
                        <x-admin.input label="Flat shipping (BDT)" name="flat_shipping"
                            type="number" step="0.01" min="0" :value="$flatShipping" placeholder="80.00"
                            help="Charged when the cart is below the free shipping threshold." />
                        <x-admin.input label="Inside Dhaka (BDT)" name="inside_dhaka_shipping"
                            type="number" step="0.01" min="0" :value="$insideDhakaShipping" placeholder="60.00" />
                        <x-admin.input label="Outside Dhaka (BDT)" name="outside_dhaka_shipping"
                            type="number" step="0.01" min="0" :value="$outsideDhakaShipping" placeholder="120.00" />
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Logo">
                    <x-admin.image-upload label="Store logo" name="logo" :current="$settings->logoUrl()" aspect="wide" />
                </x-admin.form-card>

                <x-admin.form-card title="Favicon">
                    <x-admin.image-upload label="Favicon" name="favicon" :current="$settings->faviconUrl()"
                        help="Square image recommended (32×32 or 64×64)." />
                </x-admin.form-card>
            </div>

            {{-- Contact --}}
            <div x-show="tab === 'contact'" x-cloak class="grid gap-6 lg:grid-cols-2">
                <x-admin.form-card title="Contact details" class="lg:col-span-2">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.input label="General email" name="email" type="email" :value="old('email', $settings->email)" />
                        <x-admin.input label="Support email" name="support_email" type="email" :value="old('support_email', $settings->support_email)" />
                        <x-admin.input label="Phone" name="phone" :value="old('phone', $settings->phone)" />
                    </div>
                    <div class="mt-5">
                        <x-admin.textarea label="Address" name="address" rows="3" :value="old('address', $settings->address)" />
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Social links" class="lg:col-span-2">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.input label="Instagram" name="social_instagram" type="url" :value="old('social_instagram', $social['instagram'])"
                            placeholder="https://instagram.com/..." />
                        <x-admin.input label="Facebook" name="social_facebook" type="url" :value="old('social_facebook', $social['facebook'])"
                            placeholder="https://facebook.com/..." />
                        <x-admin.input label="YouTube" name="social_youtube" type="url" :value="old('social_youtube', $social['youtube'])"
                            placeholder="https://youtube.com/..." />
                        <x-admin.input label="X (Twitter)" name="social_x" type="url" :value="old('social_x', $social['x'])"
                            placeholder="https://x.com/..." />
                    </div>
                </x-admin.form-card>
            </div>

            {{-- Regional --}}
            <div x-show="tab === 'regional'" x-cloak>
                <x-admin.form-card title="Currency & timezone">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.select label="Currency" name="currency" :value="old('currency', $settings->currency)" required>
                            @foreach ($currencies as $code => $label)
                                <option value="{{ $code }}" @selected(old('currency', $settings->currency) === $code)>{{ $label }}
                                </option>
                            @endforeach
                        </x-admin.select>

                        <x-admin.select label="Timezone" name="timezone" :value="old('timezone', $settings->timezone)" required>
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone }}" @selected(old('timezone', $settings->timezone) === $timezone)>{{ $timezone }}
                                </option>
                            @endforeach
                        </x-admin.select>
                    </div>
                </x-admin.form-card>
            </div>

            {{-- Email --}}
            <div x-show="tab === 'email'" x-cloak>
                <x-admin.form-card title="Outbound email">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.input label="From name" name="mail_from_name" :value="old('mail_from_name', $settings->mail_from_name)" />
                        <x-admin.input label="From address" name="mail_from_address" type="email"
                            :value="old('mail_from_address', $settings->mail_from_address)" />
                    </div>
                    <p class="mt-4 text-xs admin-muted">These values override the default mail sender for storefront
                        notifications when saved.</p>
                </x-admin.form-card>
            </div>

            {{-- Maintenance --}}
            <div x-show="tab === 'maintenance'" x-cloak>
                <x-admin.form-card title="Maintenance mode">
                    <x-admin.toggle label="Enable maintenance mode" name="maintenance_enabled" :checked="old('maintenance_enabled', $settings->maintenance_enabled)" />
                    <p class="mt-2 text-xs admin-muted">When enabled, the storefront shows a maintenance page. Admin
                        remains accessible.</p>
                    <div class="mt-5">
                        <x-admin.textarea label="Maintenance message" name="maintenance_message" rows="4"
                            :value="old('maintenance_message', $settings->maintenance_message)"
                            placeholder="We are performing scheduled maintenance and will be back shortly." />
                    </div>
                    <div class="mt-5">
                        <x-admin.input label="Bypass secret" name="maintenance_secret" :value="old('maintenance_secret', $settings->maintenance_secret)"
                            help="Optional secret path segment to preview the site while maintenance is active." />
                    </div>
                </x-admin.form-card>
            </div>

            {{-- SEO --}}
            <div x-show="tab === 'seo'" x-cloak class="grid gap-6 lg:grid-cols-2">
                <x-admin.form-card title="SEO defaults" class="lg:col-span-2">
                    <div class="grid gap-5">
                        <x-admin.input label="Default meta title" name="meta_title" :value="old('meta_title', $settings->meta_title)" />
                        <x-admin.textarea label="Default meta description" name="meta_description" rows="3"
                            :value="old('meta_description', $settings->meta_description)" />
                        <x-admin.input label="Meta keywords" name="meta_keywords" :value="old('meta_keywords', $settings->meta_keywords)"
                            placeholder="veteran, apparel, outdoor gear" />
                        <x-admin.input label="Google Analytics ID" name="google_analytics_id" :value="old('google_analytics_id', $settings->google_analytics_id)"
                            placeholder="G-XXXXXXXXXX" />
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Open Graph image" class="lg:col-span-2">
                    <x-admin.image-upload label="Default share image" name="og_image" :current="$settings->ogImageUrl()"
                        aspect="banner" help="Used when pages do not define their own social image." />
                </x-admin.form-card>
            </div>

            {{-- Appearance --}}
            <div x-show="tab === 'appearance'" x-cloak>
                <x-admin.form-card title="Theme colors"
                    description="Override storefront header, utility bar, button, and accent colors. Leave defaults for the standard Valor palette.">
                    <div class="grid gap-5 sm:grid-cols-2">
                        @foreach ($themeColorFields as $key => $label)
                            @php $colorValue = old("theme_colors.{$key}", $themeColors[$key] ?? ''); @endphp
                            <x-admin.field :label="$label" :name="'theme_colors.' . $key">
                                <div class="flex items-center gap-3">
                                    <input type="color" name="theme_colors[{{ $key }}]"
                                        value="{{ $colorValue }}"
                                        class="size-11 cursor-pointer rounded-[var(--radius-admin)] border admin-border bg-admin-bg admin-focus-ring">
                                    <input type="text" value="{{ $colorValue }}" readonly
                                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-bg px-3 py-2.5 font-mono text-sm admin-text">
                                </div>
                            </x-admin.field>
                        @endforeach
                    </div>
                </x-admin.form-card>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t admin-border bg-admin-bg/30 px-4 py-4 sm:px-6">
            <x-admin.button type="submit" variant="primary">Save settings</x-admin.button>
        </div>
    </div>
</form>
