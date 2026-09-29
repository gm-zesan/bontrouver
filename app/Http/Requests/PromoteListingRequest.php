<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoteListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'package_id' => ['nullable', 'exists:promotion_packages,id'],
            'package_ids' => ['nullable', 'array'],
            'package_ids.*' => ['exists:promotion_packages,id'],
            'payment_method' => ['required', 'in:stripe,points'],
            'card_token' => ['nullable', 'string'],
        ];
    }
}
