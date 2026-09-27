<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePointRulesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'earn'   => ['nullable', 'array'],
            'earn.*' => ['required', 'integer', 'min:1', 'max:5000'],
            'spend'  => ['nullable', 'array'],
            'spend.*'=> ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }
}
