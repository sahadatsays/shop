@php
    $selected = collect(old('permissions', isset($role) ? $role->permissions->pluck('id')->all() : []))->map(fn ($id) => (int) $id);
@endphp

<x-admin.input label="Role name" name="name" :value="old('name', $role->name ?? '')" required />
<x-admin.input label="Slug" name="slug" :value="old('slug', $role->slug ?? '')" help="Leave blank to generate from the role name." :disabled="($role->is_system ?? false)" />
<x-admin.textarea label="Description" name="description" rows="3">{{ old('description', $role->description ?? '') }}</x-admin.textarea>

<div>
    <p class="mb-3 text-sm font-medium admin-text">Permissions</p>
    <div class="space-y-4 rounded-[var(--radius-admin)] border admin-border p-4">
        @foreach ($permissions->groupBy('group') as $group => $items)
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider admin-muted">{{ $group }}</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($items as $permission)
                        <label class="flex items-start gap-2 rounded-[var(--radius-admin)] px-2 py-1.5 hover:bg-admin-bg/60">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked($selected->contains($permission->id))
                                   class="mt-0.5 size-4 rounded border admin-border accent-admin-brand admin-focus-ring">
                            <span class="text-sm admin-text">{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
