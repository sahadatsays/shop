<?php

namespace App\Http\Requests\Admin;

class StoreCategoryRequest extends CategoryRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }

    public function withValidator($validator): void
    {
        $this->withParentValidation($validator);
    }
}
