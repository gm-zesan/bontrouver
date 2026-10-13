<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCategoryAttributeRequest extends FormRequest
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
                'slug' => Str::slug($nameEn, '_'),
            ]);
        }
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id ?? $this->input('category_id');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name_en' => ['required', 'string', 'max:100'],
            'name_fr' => ['nullable', 'string', 'max:100'],
            'placeholder_en' => ['nullable', 'string', 'max:150'],
            'placeholder_fr' => ['nullable', 'string', 'max:150'],
            'help_text_en' => ['nullable', 'string', 'max:255'],
            'help_text_fr' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique('category_attributes', 'slug')->where(function ($query) use ($categoryId) {
                    return $query->where('category_id', $categoryId);
                }),
            ],
            'type' => ['required', 'string', Rule::in(['select', 'text', 'number', 'checkbox', 'textarea'])],
            'is_required' => ['nullable', 'boolean'],
            'is_filterable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The key must contain only lowercase letters, numbers, underscores (_), and dashes (-). No spaces or special characters allowed.',
            'slug.unique' => 'This key is already in use for this category. Please provide a unique key.',
        ];
    }
}
