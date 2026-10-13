<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'name_en',
        'name_fr',
        'code',
        'slug',
        'country_code',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'name',
    ];

    /**
     * Context-aware localized province name.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['name_fr'])) {
            return $this->attributes['name_fr'];
        }
        return $this->attributes['name_en'] ?? '';
    }

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function cities()
    {
        return $this->hasMany(City::class)->orderBy('sort_order');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function smartAlerts()
    {
        return $this->hasMany(SmartAlert::class);
    }
}
