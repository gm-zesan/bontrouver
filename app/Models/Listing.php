<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ListingStatus;

class Listing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'price',
        'price_type',
        'price_period',
        'condition',
        'city_id',
        'location_name',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'status',
        'is_featured',
        'is_sponsored',
        'featured_until',
        'sponsored_until',
        'bumped_at',
        'views_count',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => ListingStatus::class,
        'is_featured' => 'boolean',
        'is_sponsored' => 'boolean',
        'featured_until' => 'datetime',
        'sponsored_until' => 'datetime',
        'bumped_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ListingImage::class)->where('is_primary', true);
    }

    /**
     * Get the primary image URL or fallback to brand placeholder.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        if ($this->relationLoaded('primaryImage') && $this->primaryImage && !empty($this->primaryImage->image_path)) {
            return $this->primaryImage->url;
        }

        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            $first = $this->images->first();
            if ($first && !empty($first->image_path)) {
                return $first->url;
            }
        }

        if ($this->primaryImage && !empty($this->primaryImage->image_path)) {
            return $this->primaryImage->url;
        }

        $firstImage = $this->images()->first();
        if ($firstImage && !empty($firstImage->image_path)) {
            return $firstImage->url;
        }

        return asset('images/no-image.svg');
    }

    public function attributes()
    {
        return $this->hasMany(ListingAttribute::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function promotions()
    {
        return $this->hasMany(ListingPromotion::class);
    }

    public function activePromotions()
    {
        return $this->hasMany(ListingPromotion::class)->active();
    }

    public function isFeatured(): bool
    {
        return $this->is_featured && ($this->featured_until === null || $this->featured_until->isFuture());
    }

    public function isSponsored(): bool
    {
        return $this->is_sponsored && ($this->sponsored_until === null || $this->sponsored_until->isFuture());
    }

    public function isBumped(): bool
    {
        return $this->bumped_at !== null && $this->bumped_at->gt(now()->subDays(3));
    }

    /**
     * Scope active listings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ListingStatus::ACTIVE);
    }

    /**
     * Scope active featured listings.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')->orWhere('featured_until', '>=', now());
            });
    }

    /**
     * Scope active sponsored listings.
     */
    public function scopeSponsored($query)
    {
        return $query->where('is_sponsored', true)
            ->where(function ($q) {
                $q->whereNull('sponsored_until')->orWhere('sponsored_until', '>=', now());
            });
    }

    /**
     * Scope recently bumped listings.
     */
    public function scopeBumped($query)
    {
        return $query->whereNotNull('bumped_at')
            ->orderByDesc('bumped_at');
    }

    /**
     * Scope listings within a specified radius (km) from target coordinates using Haversine formula.
     */
    public function scopeWithinRadius($query, float $latitude, float $longitude, float $radiusKm)
    {
        $earthRadius = 6371; // km
        return $query->selectRaw(
            "listings.*, ( ? * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance_km",
            [$earthRadius, $latitude, $longitude, $latitude]
        )->having('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km', 'asc');
    }
}
