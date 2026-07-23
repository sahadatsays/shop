<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('media.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
            'title' => ['nullable', 'string', 'max:160'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
