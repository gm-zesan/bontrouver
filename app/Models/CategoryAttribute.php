<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    protected $fillable = [
        'category_id',
        'name_en',
        'name_fr',
        'slug',
        'type',
        'placeholder_en',
        'placeholder_fr',
        'help_text_en',
        'help_text_fr',
        'is_required',
        'is_filterable',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'name',
        'placeholder',
        'help_text',
    ];

    /**
     * Context-aware localized attribute name.
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
     * Context-aware localized placeholder.
     */
    public function getPlaceholderAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['placeholder_fr'])) {
            return $this->attributes['placeholder_fr'];
        }
        return $this->attributes['placeholder_en'] ?? null;
    }

    /**
     * Context-aware localized help text.
     */
    public function getHelpTextAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['help_text_fr'])) {
            return $this->attributes['help_text_fr'];
        }
        return $this->attributes['help_text_en'] ?? null;
    }

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function options()
    {
        return $this->hasMany(AttributeOption::class)->orderBy('sort_order');
    }
}
