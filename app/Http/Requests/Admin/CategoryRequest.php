<?php

namespace App\Http\Requests\Admin;

use App\Enums\CategoryStatus;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(?int $categoryId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('categories', 'slug')->ignore($categoryId)->whereNull('deleted_at'),
            ],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['required', Rule::enum(CategoryStatus::class)],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:4096'],
        ];
    }

    protected function withParentValidation(Validator $validator, ?int $categoryId = null): void
    {
        $validator->after(function (Validator $validator) use ($categoryId): void {
            $parentId = $this->input('parent_id');

            if (! $parentId) {
                return;
            }

            if ($categoryId && (int) $parentId === $categoryId) {
                $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
            }

            $parent = Category::query()->find($parentId);

            if ($parent?->parent_id) {
                $validator->errors()->add('parent_id', 'Only top-level categories can be selected as parents.');
            }
        });
    }
}
