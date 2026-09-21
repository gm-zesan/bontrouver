<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reportable_type' => ['required', 'string', 'in:listing,user,companionship,companionship_request'],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', 'string', new \Illuminate\Validation\Rules\Enum(\App\Enums\ReportReason::class)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'reportable_type.required' => 'Report target type is missing.',
            'reportable_id.required' => 'Target item ID is required.',
            'reason.required' => 'Please select a reason for reporting.',
        ];
    }
}
