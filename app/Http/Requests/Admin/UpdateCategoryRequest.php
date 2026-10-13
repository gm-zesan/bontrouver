<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $nameEn = $this->name_en ?? $this->name;
        if (!empty($nameEn)) {
            $this->merge(['name_en' => $nameEn]);
        }
        if (empty($this->slug) && !empty($nameEn)) {
            $this->merge([
                'slug' => Str::slug($nameEn),
            ]);
        }
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id ?? $this->input('id');

        return [
            'name_en' => ['required', 'string', 'max:100'],
            'name_fr' => ['nullable', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:120', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value && (int)$value === (int)$categoryId) {
                        $fail('A category cannot be its own parent.');
                    }
                },
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_fr' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
