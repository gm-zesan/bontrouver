<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isModerator();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $group = $this->input('group', 'general');

        return match ($group) {
            'general' => [
                'group'            => ['required', 'string', 'in:general,branding,seo,marketplace'],
                'site_name'        => ['required', 'string', 'max:100'],
                'site_tagline'     => ['nullable', 'string', 'max:255'],
                'contact_email'    => ['required', 'email', 'max:150'],
                'contact_phone'    => ['nullable', 'string', 'max:50'],
                'contact_address'  => ['nullable', 'string', 'max:255'],
                'footer_copyright' => ['nullable', 'string', 'max:255'],
            ],
            'branding' => [
                'group'            => ['required', 'string', 'in:general,branding,seo,marketplace'],
                'site_logo_light'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
                'site_logo_dark'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
                'site_favicon'     => ['nullable', 'mimes:jpeg,png,jpg,ico,svg', 'max:2048'],
                'og_default_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            ],
            'seo' => [
                'group'            => ['required', 'string', 'in:general,branding,seo,marketplace'],
                'meta_title'       => ['required', 'string', 'max:150'],
                'meta_description' => ['nullable', 'string', 'max:500'],
                'meta_keywords'    => ['nullable', 'string', 'max:500'],
                'geo_region'       => ['nullable', 'string', 'max:50'],
                'geo_placename'    => ['nullable', 'string', 'max:100'],
                'geo_position'     => ['nullable', 'string', 'max:100'],
                'social_twitter'   => ['nullable', 'url', 'max:255'],
                'social_facebook'  => ['nullable', 'url', 'max:255'],
                'social_instagram' => ['nullable', 'url', 'max:255'],
                'social_linkedin'  => ['nullable', 'url', 'max:255'],
            ],
            'marketplace' => [
                'group'                  => ['required', 'string', 'in:general,branding,seo,marketplace'],
                'auto_approve_listings'  => ['nullable', 'boolean'],
                'max_images_per_listing' => ['required', 'integer', 'min:1', 'max:50'],
                'max_upload_size_mb'     => ['required', 'integer', 'min:1', 'max:100'],
                'listing_expiry_days'    => ['required', 'integer', 'min:1', 'max:365'],
                'free_listings_limit'    => ['required', 'integer', 'min:1', 'max:1000'],
            ],
            default => [
                'group' => ['required', 'string', 'in:general,branding,seo,marketplace'],
            ],
        };
    }
}
