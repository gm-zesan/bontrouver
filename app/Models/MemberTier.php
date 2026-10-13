<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberTier extends Model
{
    public const CACHE_KEY = 'member_tiers';

    protected $fillable = [
        'name_en',
        'name_fr',
        'icon',
        'badge_color',
        'badge_class',
        'min_points',
        'max_points',
        'description_en',
        'description_fr',
        'perks_en',
        'perks_fr',
    ];

    protected $appends = [
        'name',
        'description',
        'perks',
    ];

    /**
     * Context-aware localized tier name.
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
     * Context-aware localized tier description.
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
     * Context-aware localized perks list.
     */
    public function getPerksAttribute(): array
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['perks_fr'])) {
            $val = is_string($this->attributes['perks_fr']) ? json_decode($this->attributes['perks_fr'], true) : $this->attributes['perks_fr'];
            if (is_array($val) && !empty($val)) {
                return $val;
            }
        }
        $default = $this->attributes['perks_en'] ?? [];
        return is_string($default) ? json_decode($default, true) ?? [] : (is_array($default) ? $default : []);
    }

    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
        'perks_en'   => 'array',
        'perks_fr'   => 'array',
    ];

    /**
     * Get clean formatted display name without leading emoji.
     */
    public function getCleanNameAttribute(): string
    {
        return trim(preg_replace('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F1E6}-\x{1F1FF}]\s*/u', '', $this->name));
    }
}
