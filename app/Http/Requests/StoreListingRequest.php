<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'                      => ['required', 'string', 'min:6', 'max:100'],
            'category_slug'              => ['required', 'string'],
            'subcategory_slug'           => ['nullable', 'string'],
            'price'                      => ['nullable', 'numeric', 'min:0'],
            'price_type'                 => ['required', 'in:fixed,negotiable,free,contact'],
            'condition'                  => ['nullable', 'string'],
            'description'                => ['required', 'string', 'min:15', 'max:5000'],
            'city'                       => ['required', 'string', 'max:100'],
            'province'                   => ['required', 'string', 'max:10'],
            'postal_code'                => ['nullable', 'string', 'max:10'],
            'neighbourhood'              => ['nullable', 'string', 'max:100'],
            'latitude'                   => ['nullable', 'numeric'],
            'longitude'                  => ['nullable', 'numeric'],
            'show_approximate_location'  => ['nullable', 'boolean'],
            'delivery_options'           => ['nullable', 'array'],
            'contact_preference'         => ['nullable', 'array'],
            'images'                     => ['nullable', 'array', 'max:' . site_setting('max_images_per_listing', 10)],
            'attributes'                 => ['nullable', 'array'],
            'promotions'                 => ['nullable', 'array'],
            'payment_method'             => ['nullable', 'string', 'in:card,points,stripe'],
            'stripe_cardholder_name'     => ['nullable', 'string', 'max:150'],
            'stripe_card_number'         => ['nullable', 'string', 'max:30'],
            'stripe_card_expiry'         => ['nullable', 'string', 'max:10'],
            'stripe_card_cvc'            => ['nullable', 'string', 'max:6'],
            'stripe_postal_code'         => ['nullable', 'string', 'max:12'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.min'        => 'Your ad title must be at least 6 characters.',
            'description.min'  => 'Please write a more detailed description (at least 15 characters).',
            'price_type.in'    => 'Please select a valid pricing type.',
        ];
    }
}
