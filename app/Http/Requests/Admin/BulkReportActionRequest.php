<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkReportActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->isModerator());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:resolve,dismiss,delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:reports,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Please select a bulk action.',
            'action.in' => 'Selected bulk action is invalid.',
            'ids.required' => 'Please select at least one report.',
            'ids.min' => 'Please select at least one report.',
            'ids.*.exists' => 'One or more selected reports do not exist.',
        ];
    }
}
