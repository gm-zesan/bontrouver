<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterMerchantRequest extends FormRequest
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
        $maxSizeKb = (int) site_setting('max_image_upload_size_kb', 25600);

        return [
            'business_name'         => ['required', 'string', 'max:255'],
            'contact_name'          => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'                 => ['required', 'string', 'max:30'],
            'city'                  => ['required', 'string', 'max:100'],
            'province'              => ['required', 'string', 'max:50'],
            'address'               => ['required', 'string', 'max:255'],
            'postal_code'           => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/i'],
            'business_category'     => ['required', 'string', 'max:100'],
            'dealer_license_number' => ['required', 'string', 'max:100'],
            'website_url'           => ['nullable', 'url', 'max:255'],
            'business_bio'          => ['nullable', 'string', 'max:2000'],
            'operating_hours'       => ['nullable', 'array'],
            'license_document'      => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', "max:{$maxSizeKb}"],
            'terms'                 => ['accepted'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'business_name.required'         => 'Please provide your legal or trade business / dealership name.',
            'contact_name.required'          => 'Please specify a primary business contact person.',
            'phone.required'                 => 'A business telephone number is required for merchant onboarding.',
            'address.required'               => 'Please provide your physical storefront, lot, or office address.',
            'postal_code.regex'              => 'Please provide a valid Canadian Postal Code (e.g. H2X 1Y4 or M5V 2T6).',
            'business_category.required'     => 'Please select your commercial category.',
            'dealer_license_number.required' => 'Business license, OMVIC/AMVOQ, or corporate registration ID is required.',
            'license_document.max'           => 'Verification document cannot exceed 25MB.',
            'terms.accepted'                 => 'You must agree to Bontrouver Merchant Terms & Conditions.',
        ];
    }
}
