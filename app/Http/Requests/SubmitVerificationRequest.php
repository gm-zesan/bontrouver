<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'document_type' => ['nullable', 'string', 'in:government_id,drivers_license,passport,dealer_license'],
            'document' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:10240'],
            'id_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'document.mimes' => 'Accepted document formats are JPG, PNG, WEBP, or PDF.',
            'document.max' => 'The document file size must not exceed 10MB.',
            'document_type.in' => 'Please select a valid Canadian document type.',
        ];
    }
}
