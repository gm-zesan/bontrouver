<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslateListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'target_locale' => ['required', 'string', 'in:en,fr'],
            'scope'         => ['sometimes', 'string', 'in:card,hero,detail'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'target_locale.required' => 'The target locale is required.',
            'target_locale.in'       => 'The target locale must be either English (en) or French (fr).',
            'scope.in'               => 'The scope must be card, hero, or detail.',
        ];
    }
}
