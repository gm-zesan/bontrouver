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

    public function rules(): array
    {
        $provinceId = $this->route('province') instanceof Province 
            ? $this->route('province')->id 
            : $this->route('province');

        return [
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['required', 'string', 'max:4', Rule::unique('provinces', 'code')->ignore($provinceId)],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['nullable', 'boolean'],
        ];
    }
}
