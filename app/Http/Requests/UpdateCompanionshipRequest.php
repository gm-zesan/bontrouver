<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanionshipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $meetup = $this->route('meetup') ?? $this->route('id');
        if (is_numeric($meetup)) {
            $meetup = \App\Models\CompanionshipRequest::find($meetup);
        }
        
        return $meetup && $this->user()->can('update', $meetup);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'meetup_date_time' => ['required', 'date', 'after:now'],
            'location_name' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'headcount_limit' => ['nullable', 'integer', 'min:2', 'max:100'],
            'expense_type' => ['required', 'string', 'in:free,split,host_pays'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'meetup_date_time.after' => 'The meetup date and time must be in the future.',
            'expense_type.in' => 'Please select a valid expense type (Free, Split Bill, Host Pays).',
        ];
    }
}
