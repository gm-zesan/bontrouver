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
     * Get safe CSS icon class string with library prefix handling.
     */
    public function getIconClassAttribute(): string
    {
        if (empty($this->icon)) {
            return $this->parent_id ? 'ri-file-list-line' : 'ri-folder-3-fill';
        }

        if (str_starts_with($this->icon, 'bi-') && !str_starts_with($this->icon, 'bi bi-')) {
            return 'bi ' . $this->icon;
        }

        return $this->icon;
    }

    /**
     * Get the category tree hierarchically supporting arbitrary levels of depth.
     */
    public static function getTree(): array
    {
        $allCategories = self::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $grouped = $allCategories->groupBy('parent_id');

        $buildTree = function ($parentId) use (&$buildTree, $grouped) {
            $branch = [];
            $children = $grouped->get($parentId, collect());
            foreach ($children as $category) {
                $catArray = $category->toArray();
                $nested = $buildTree($category->id);
                $catArray['children'] = $nested;
                $catArray['subcategories'] = $nested; // backward-compatibility alias
                $branch[] = $catArray;
            }
            return $branch;
        };

        $tree = [];
        $roots = $grouped->get(null, $grouped->get('', collect()));
        foreach ($roots as $root) {
            $rootArray = $root->toArray();
            $children = $buildTree($root->id);
            $rootArray['children'] = $children;
            $rootArray['subcategories'] = $children;
            $tree[$root->slug] = $rootArray;
        }

        return $tree;
    }
}
