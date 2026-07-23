<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;

class UpdateCategoryRequest extends CategoryRequest
{
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
