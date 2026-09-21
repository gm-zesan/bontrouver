<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function attributes()
    {
        return $this->hasMany(CategoryAttribute::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Get the full breadcrumb path (e.g. Vehicles → Cars & Trucks).
     */
    public function getFullPathAttribute(): string
    {
        if ($this->relationLoaded('parent') && $this->parent) {
            return $this->parent->name . ' → ' . $this->name;
        }
        if ($this->parent_id && $this->parent) {
            return $this->parent->name . ' → ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Get the category tree hierarchically.
     */
    public static function getTree()
    {
        // Eager load nested children up to 2 levels deep
        $categories = self::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order')->with(['children' => function($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                }]);
            }])
            ->get();

        // Convert the collection to a keyed array by slug for easy access
        $tree = [];
        foreach ($categories as $category) {
            $tree[$category->slug] = $category->toArray();
        }

        return $tree;
    }
}
