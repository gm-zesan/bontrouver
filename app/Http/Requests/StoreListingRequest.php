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
            'title'                      => ['required', 'string', 'min:6', 'max:150'],
            'category_id'                => ['required_without:category_slug', 'nullable', 'integer', 'exists:categories,id'],
            'category_slug'              => ['nullable', 'string'],
            'subcategory_slug'           => ['nullable', 'string'],
            'price'                      => ['nullable', 'numeric', 'min:0'],
            'price_type'                 => ['required', 'in:fixed,negotiable,free,contact'],
            'price_period'               => ['nullable', 'string', 'in:one_time,hour,day,week,month,year'],
            'condition'                  => ['nullable', 'string'],
            'description'                => ['required', 'string', 'min:15', 'max:15000'],
            'province_id'                => ['required_without:province', 'nullable', 'exists:provinces,id'],
            'city_id'                    => ['required_without:city', 'nullable', 'exists:cities,id'],
            'city'                       => ['required_without:city_id', 'nullable', 'string', 'max:100'],
            'province'                   => ['required_without:province_id', 'nullable', 'string', 'max:10'],
            'postal_code'                => ['nullable', 'string', 'max:10'],
            'location_name'              => ['nullable', 'string', 'max:100'],
            'neighbourhood'              => ['nullable', 'string', 'max:100'],
            'latitude'                   => ['nullable', 'numeric'],
            'longitude'                  => ['nullable', 'numeric'],
            'images'                     => ['nullable', 'array', 'max:' . site_setting('max_images_per_listing', 15)],
            'images.*'                   => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/jpg'];
                        if (!in_array($value->getMimeType(), $allowedMimes)) {
                            $fail('Each image must be a valid image file (JPG, PNG, or WEBP).');
                        }
                        $maxKb = (int) site_setting('max_image_upload_size_kb', 25600);
                        if ($value->getSize() > $maxKb * 1024) {
                            $fail('Image exceeds the maximum allowed upload size.');
                        }
                    } elseif (is_string($value)) {
                        if (!str_starts_with($value, 'data:image/') && !str_starts_with($value, '/storage/') && !str_starts_with($value, 'http://') && !str_starts_with($value, 'https://')) {
                            $fail('Invalid image format provided.');
                        }
                    } else {
                        $fail('Invalid image data provided.');
                    }
                },
            ],
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
