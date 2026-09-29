<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingImage extends Model
{
    protected $fillable = [
        'listing_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Get accessible image URL with fallback to brand placeholder.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/no-image.svg');
        }
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }
        if (str_starts_with($this->image_path, '/storage/')) {
            return asset(ltrim($this->image_path, '/'));
        }
        if (str_starts_with($this->image_path, 'storage/')) {
            return asset($this->image_path);
        }
        return asset('storage/' . ltrim($this->image_path, '/'));
    }
}
