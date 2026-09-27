<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryAttributeRequest extends FormRequest
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
        $attribute = $this->route('attribute');
        $attributeId = $attribute?->id ?? $this->input('id');
        $categoryId = $attribute?->category_id ?? $this->input('category_id');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('category_attributes', 'slug')
                    ->where(function ($query) use ($categoryId) {
                        return $query->where('category_id', $categoryId);
                    })
                    ->ignore($attributeId),
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
}
