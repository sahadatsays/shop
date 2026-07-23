<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CropMediaRequest extends FormRequest
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
            'x' => ['required', 'numeric', 'min:0'],
            'y' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:1'],
            'height' => ['required', 'numeric', 'min:1'],
            'scale' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
