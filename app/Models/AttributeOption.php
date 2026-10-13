<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    protected $fillable = [
        'category_attribute_id',
        'label_en',
        'label_fr',
        'value',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'label',
    ];

    /**
     * Context-aware localized option label.
     */
    public function getLabelAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['label_fr'])) {
            return $this->attributes['label_fr'];
        }
        return $this->attributes['label_en'] ?? '';
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }
}
