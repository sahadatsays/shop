<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class UpdateCategoryRequest extends CategoryRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()?->hasPermission('categories.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $category = $this->route('category');

        return $this->baseRules($category instanceof Category ? $category->id : (int) $category);
    }

    public function withValidator($validator): void
    {
        $category = $this->route('category');
        $id = $category instanceof Category ? $category->id : (int) $category;

        $this->withParentValidation($validator, $id);
    }
}
