<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100'],
            'icon'        => ['nullable', 'string', 'max:50'],
            'badge_color' => ['nullable', 'string', 'max:50'],
            'badge_class' => ['nullable', 'string', 'max:100'],
            'min_points'  => ['required', 'integer', 'min:0'],
            'max_points'  => ['nullable', 'integer', 'gte:min_points'],
            'description' => ['nullable', 'string', 'max:500'],
            'perks'       => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'max_points.gte' => 'The maximum points threshold must be greater than or equal to the minimum points threshold.',
        ];
    }
}
