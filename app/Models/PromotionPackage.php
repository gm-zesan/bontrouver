<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromotionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_fr',
        'slug',
        'type',
        'badge_text_en',
        'badge_text_fr',
        'badge_color',
        'badge_icon',
        'price',
        'point_cost',
        'duration_days',
        'description_en',
        'description_fr',
        'features_en',
        'features_fr',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'name',
        'badge_text',
        'description',
        'features',
    ];

    /**
     * Context-aware localized package name.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['name_fr'])) {
            return $this->attributes['name_fr'];
        }
        return $this->attributes['name_en'] ?? '';
    }

    /**
     * Context-aware localized badge text.
     */
    public function getBadgeTextAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['badge_text_fr'])) {
            return $this->attributes['badge_text_fr'];
        }
        return $this->attributes['badge_text_en'] ?? null;
    }

    /**
     * Context-aware localized description.
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['description_fr'])) {
            return $this->attributes['description_fr'];
        }
        return $this->attributes['description_en'] ?? null;
    }

    /**
     * Context-aware localized features list.
     */
    public function getFeaturesAttribute(): array
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['features_fr'])) {
            $val = is_string($this->attributes['features_fr']) ? json_decode($this->attributes['features_fr'], true) : $this->attributes['features_fr'];
            if (is_array($val) && !empty($val)) {
                return $val;
            }
        }
        $default = $this->attributes['features_en'] ?? [];
        return is_string($default) ? json_decode($default, true) ?? [] : (is_array($default) ? $default : []);
    }

    protected $casts = [
        'price' => 'decimal:2',
        'point_cost' => 'integer',
        'duration_days' => 'integer',
        'features' => 'array',
        'features_en' => 'array',
        'features_fr' => 'array',
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
