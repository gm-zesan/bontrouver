<?php

namespace App\Http\Requests\Admin;

use App\Models\Province;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->isModerator());
    }

    protected function prepareForValidation(): void
    {
        $nameEn = $this->name_en ?? $this->name;
        if (!empty($nameEn)) {
            $this->merge(['name_en' => $nameEn]);
        }
    }

    public function rules(): array
    {
        $provinceId = $this->route('province') instanceof Province 
            ? $this->route('province')->id 
            : $this->route('province');

        return [
            'name_en'    => ['required', 'string', 'max:255'],
            'name_fr'    => ['nullable', 'string', 'max:255'],
            'code'       => ['required', 'string', 'max:10', Rule::unique('provinces', 'code')->ignore($provinceId)],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['nullable', 'boolean'],
        ];
    }
}
