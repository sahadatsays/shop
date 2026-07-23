<x-admin.input label="Permission name" name="name" :value="old('name', $permission->name ?? '')" required />
<x-admin.input label="Slug" name="slug" :value="old('slug', $permission->slug ?? '')" help="Leave blank to generate from the permission name." :disabled="($permission->is_system ?? false)" />
<x-admin.input label="Group" name="group" :value="old('group', $permission->group ?? '')" list="permission-groups" required />
<datalist id="permission-groups">
    @foreach ($groups as $group)
        <option value="{{ $group }}"></option>
    @endforeach
</datalist>
<x-admin.textarea label="Description" name="description" rows="3">{{ old('description', $permission->description ?? '') }}</x-admin.textarea>
