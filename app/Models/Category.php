<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    use HasFactory;

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
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Parent category relationship.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Direct children categories relationship.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Recursive nested children relationship (infinite depth).
     */
    public function recursiveChildren(): HasMany
    {
        return $this->children()->with('recursiveChildren');
    }

    /**
     * Scope for Root (Level 1) Categories.
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this category is a root category.
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get all ancestors up to the root parent.
     */
    public function getAncestors(): Collection
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    /**
     * Get category URL attribute.
     */
    public function getUrlAttribute(): string
    {
        if ($this->isRoot()) {
            return url('/category/' . $this->slug);
        }

        $ancestors = $this->getAncestors();
        $root = $ancestors->first();

        if (!$root) {
            return url('/category/' . $this->slug);
        }

        $url = url('/category/' . $root->slug);
        $remaining = $ancestors->slice(1)->push($this)->values();

        if ($remaining->isNotEmpty()) {
            $params = [];
            foreach ($remaining as $idx => $item) {
                if ($idx === 0) {
                    $params['sub'] = $item->slug;
                } elseif ($idx === 1) {
                    $params['child'] = $item->slug;
                } else {
                    $params['lvl' . ($idx + 2)] = $item->slug;
                }
            }
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Transform an Eloquent Category tree into standard array format.
     */
    public function toTreeArray(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon ?? 'bi-tag',
            'description' => $this->description ?? '',
            'url' => $this->url,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->relationLoaded('children') || $this->relationLoaded('recursiveChildren')) {
            $childrenCollection = $this->recursiveChildren ?? $this->children;
            if ($childrenCollection && $childrenCollection->isNotEmpty()) {
                $data['children'] = $childrenCollection->map(function (Category $child) {
                    return $child->toTreeArray();
                })->all();
            } else {
                $data['children'] = [];
            }
        }

        return $data;
    }

    /**
     * Get complete category tree as formatted array.
     */
    public static function getTree(): array
    {
        $roots = static::roots()
            ->active()
            ->with('recursiveChildren')
            ->get();

        $tree = [];
        foreach ($roots as $root) {
            $tree[$root->slug] = $root->toTreeArray();
        }

        return $tree;
    }
}
