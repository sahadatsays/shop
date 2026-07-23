<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('media.manage') ?? false;
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'You do not have permission to upload media.',
        ], 403));
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->first() ?? 'The uploaded files are invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Select at least one file to upload.',
            'files.min' => 'Select at least one file to upload.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.max' => 'Each file must be 10 MB or smaller.',
            'files.*.mimes' => 'Only JPG, PNG, WebP, GIF, SVG, and PDF files are allowed.',
            'folder_id.exists' => 'The selected folder no longer exists.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,svg,pdf'],
            'folder_id' => ['nullable', 'integer', 'exists:media_folders,id'],
        ];
    }
}
