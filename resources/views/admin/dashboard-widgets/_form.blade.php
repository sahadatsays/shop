@php
    $selectedType = old('type', $widget->type?->value);
    $selectedPermission = old('permission', $widget->permission);
    $roleValues = collect(old('roles', $selectedRoles ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

<div class="space-y-6">
    <x-admin.form-card title="Identity" description="How this widget is identified and labelled.">
        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.input
                name="key"
                label="Widget key"
                :value="$widget->key"
                required
                help="Lowercase, hyphenated. Must match a registered provider to render data."
                placeholder="e.g. sales-stats"
            />
            <x-admin.input name="name" label="Display name" :value="$widget->name" required placeholder="e.g. Today's Performance" />
        </div>

        <x-admin.textarea name="description" label="Description" class="mt-5" :help="'Short summary shown in the widget catalog.'">{{ old('description', $widget->description) }}</x-admin.textarea>

        <x-admin.input name="icon" label="Icon (SVG path data)" class="mt-5" :value="$widget->icon" help="Optional. The 'd' attribute of an SVG path." />
    </x-admin.form-card>

    <x-admin.form-card title="Layout & behaviour" description="Grid size, ordering, and refresh cadence.">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <x-admin.select name="type" label="Type" required>
                @foreach ($types as $type)
                    <option value="{{ $type['value'] }}" @selected($selectedType === $type['value'])>{{ $type['label'] }}</option>
                @endforeach
            </x-admin.select>

            <x-admin.input name="category" label="Category" :value="$widget->category ?? 'general'" required placeholder="e.g. sales" />

            <x-admin.input type="number" name="display_order" label="Display order" :value="$widget->display_order ?? 0" required min="0" />

            <x-admin.input type="number" name="width" label="Width (columns 3–12)" :value="$widget->width ?? 6" required min="3" max="12" />

            <x-admin.input type="number" name="height" label="Height (rows 1–6)" :value="$widget->height ?? 1" required min="1" max="6" />

            <x-admin.input type="number" name="refresh_interval" label="Refresh (seconds)" :value="$widget->refresh_interval" min="0" help="Blank disables auto-refresh." />
        </div>

        <div class="mt-5">
            <x-admin.checkbox name="is_active" label="Enabled" :checked="(bool) ($widget->is_active ?? true)" help="Disabled widgets are hidden from every dashboard." />
        </div>
    </x-admin.form-card>

    <x-admin.form-card title="Access control" description="Restrict this widget by permission and/or role.">
        <x-admin.select name="permission" label="Required permission">
            <option value="">Any dashboard user</option>
            @foreach ($permissions as $permission)
                <option value="{{ $permission->slug }}" @selected($selectedPermission === $permission->slug)>{{ $permission->group }} — {{ $permission->name }}</option>
            @endforeach
        </x-admin.select>

        <fieldset class="mt-5">
            <legend class="mb-2 block text-sm font-medium admin-text">Role access</legend>
            <p class="mb-3 text-xs admin-muted">Leave all unchecked to allow every role (still subject to the permission above).</p>
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($roles as $role)
                    <x-admin.checkbox
                        name="roles[]"
                        :label="$role->name"
                        :value="(string) $role->id"
                        :checked="in_array($role->id, $roleValues, true)"
                    />
                @endforeach
            </div>
        </fieldset>
    </x-admin.form-card>

    <div class="flex items-center justify-end gap-3">
        <x-admin.button variant="secondary" :href="route('admin.dashboard-widgets.index')">Cancel</x-admin.button>
        <x-admin.button type="submit">{{ $submitLabel ?? 'Save widget' }}</x-admin.button>
    </div>
</div>
