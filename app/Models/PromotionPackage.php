<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromotionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'badge_text',
        'badge_color',
        'badge_icon',
        'price',
        'point_cost',
        'duration_days',
        'description',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'point_cost' => 'integer',
        'duration_days' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function listingPromotions(): HasMany
    {
        return $this->hasMany(ListingPromotion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('price');
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
