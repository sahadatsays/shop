<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreMediaFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('media.folders.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $folderId = $this->route('folder')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:media_folders,id', Rule::notIn([$folderId])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
