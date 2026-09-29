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
            'category_id'                => ['required_without:category_slug', 'nullable', 'integer', 'exists:categories,id'],
            'category_slug'              => ['nullable', 'string'],
            'subcategory_slug'           => ['nullable', 'string'],
            'price'                      => ['nullable', 'numeric', 'min:0'],
            'price_type'                 => ['required', 'in:fixed,negotiable,free,contact'],
            'price_period'               => ['nullable', 'string', 'in:one_time,hour,day,week,month,year'],
            'condition'                  => ['nullable', 'string'],
            'description'                => ['required', 'string', 'min:15', 'max:5000'],
            'city'                       => ['required', 'string', 'max:100'],
            'province'                   => ['required', 'string', 'max:10'],
            'postal_code'                => ['nullable', 'string', 'max:10'],
            'location_name'              => ['nullable', 'string', 'max:100'],
            'neighbourhood'              => ['nullable', 'string', 'max:100'],
            'latitude'                   => ['nullable', 'numeric'],
            'longitude'                  => ['nullable', 'numeric'],
            'images'                     => ['nullable', 'array', 'max:' . site_setting('max_images_per_listing', 10)],
            'attributes'                 => ['nullable', 'array'],
            'promotions'                 => ['nullable', 'array'],
            'payment_method'             => ['nullable', 'string', 'in:card,points,stripe'],
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
