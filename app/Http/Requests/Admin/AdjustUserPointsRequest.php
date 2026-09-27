<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdjustUserPointsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id'     => ['required', 'exists:users,id'],
            'type'        => ['required', 'in:award,deduct'],
            'amount'      => ['required', 'integer', 'min:1', 'max:50000'],
            'reason'      => ['required', 'string', 'min:5', 'max:255'],
            'action_type' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Point adjustment amount must be at least 1 point.',
            'reason.min' => 'Please provide a descriptive reason for this point adjustment (at least 5 characters).',
        ];
    }
}
