<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_key',
        'name_en',
        'name_fr',
        'type',
        'points',
        'category',
        'description_en',
        'description_fr',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'name',
        'description',
    ];

    /**
     * Context-aware localized rule name.
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
     * Context-aware localized rule description.
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['description_fr'])) {
            return $this->attributes['description_fr'];
        }
        return $this->attributes['description_en'] ?? null;
    }

    protected $casts = [
        'points'     => 'integer',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEarn(Builder $query): Builder
    {
        return $query->where('type', 'earn');
    }

    public function scopeSpend(Builder $query): Builder
    {
        return $query->where('type', 'spend');
    }
}
