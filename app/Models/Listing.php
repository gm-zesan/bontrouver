<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'views_count',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_sponsored' => 'boolean',
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

    /**
     * Scope active listings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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
