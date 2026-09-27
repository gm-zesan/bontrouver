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
        if (empty($this->slug) && !empty($this->name)) {
            $this->merge([
                'slug' => Str::slug($this->name, '_'),
            ]);
        }
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id ?? $this->input('category_id');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
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
            'options.*' => ['nullable', 'string', 'max:150'],
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
