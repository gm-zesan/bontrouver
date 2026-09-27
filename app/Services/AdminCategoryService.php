<?php

namespace App\Services;

use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCategoryService
{
    /**
     * Get Eloquent Query for Categories with filtering and eager loaded counts.
     */
    public function getCategoriesQuery(array $filters = []): Builder
    {
        $query = Category::query()
            ->with(['parent'])
            ->withCount(['children', 'attributes', 'listings']);

        if (!empty($filters['search'])) {
            $search = is_array($filters['search']) ? ($filters['search']['value'] ?? '') : $filters['search'];
            $search = trim((string) $search);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
        }

        if (isset($filters['level']) && $filters['level'] !== '' && $filters['level'] !== 'all') {
            if ($filters['level'] === 'root') {
                $query->whereNull('parent_id');
            } elseif ($filters['level'] === 'child') {
                $query->whereNotNull('parent_id');
            }
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== 'all') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query;
    }

    /**
     * Get all parent (root) categories for select dropdowns.
     */
    public function getParentCategories()
    {
        return Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get full hierarchical category tree with counts.
     */
    public function getTreeData()
    {
        return Category::whereNull('parent_id')
            ->withCount(['children', 'attributes', 'listings'])
            ->with(['children' => function ($q) {
                $q->withCount(['attributes', 'listings'])
                  ->orderBy('sort_order')
                  ->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get KPI Statistics for Categories & Attributes.
     */
    public function getStats(): array
    {
        $total = Category::count();
        $rootCount = Category::whereNull('parent_id')->count();
        $childCount = Category::whereNotNull('parent_id')->count();
        $activeCount = Category::where('is_active', true)->count();
        $totalAttributes = CategoryAttribute::count();

        return [
            'total' => $total,
            'root' => $rootCount,
            'child' => $childCount,
            'active' => $activeCount,
            'total_attributes' => $totalAttributes,
        ];
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return Category::create($data);
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Category $category, array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $category->update($data);

        return $category;
    }

    /**
     * Delete a category safely.
     */
    public function deleteCategory(Category $category): array
    {
        $childrenCount = $category->children()->count();
        $listingsCount = $category->listings()->count();

        if ($childrenCount > 0) {
            return [
                'success' => false,
                'message' => "Cannot delete '{$category->name}' because it contains {$childrenCount} subcategories. Delete or reassign its subcategories first.",
            ];
        }

        if ($listingsCount > 0) {
            return [
                'success' => false,
                'message' => "Cannot delete '{$category->name}' because it is linked to {$listingsCount} active marketplace listings.",
            ];
        }

        // Delete associated attributes and options
        foreach ($category->attributes as $attr) {
            $attr->options()->delete();
            $attr->delete();
        }

        $category->delete();

        return [
            'success' => true,
            'message' => "Category '{$category->name}' was deleted successfully.",
        ];
    }

    /**
     * Toggle Category Active Status.
     */
    public function toggleCategoryStatus(Category $category): bool
    {
        $category->is_active = !$category->is_active;
        $category->save();

        return $category->is_active;
    }

    /**
     * Create a custom attribute for a category.
     */
    public function createAttribute(Category $category, array $data): CategoryAttribute
    {
        return DB::transaction(function () use ($category, $data) {
            $data['category_id'] = $category->id;
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name'], '_');
            }

            $data['is_required'] = isset($data['is_required']) ? (bool) $data['is_required'] : false;
            $data['is_filterable'] = isset($data['is_filterable']) ? (bool) $data['is_filterable'] : false;
            $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;

            if (empty($data['sort_order']) || (int) $data['sort_order'] <= 0) {
                $maxOrder = CategoryAttribute::where('category_id', $category->id)->max('sort_order') ?? 0;
                $data['sort_order'] = $maxOrder + 1;
            }

            $options = $data['options'] ?? [];
            unset($data['options']);

            $attribute = CategoryAttribute::create($data);

            if ($attribute->type === 'select' && !empty($options)) {
                $this->saveAttributeOptions($attribute, $options);
            }

            return $attribute;
        });
    }

    /**
     * Update an attribute.
     */
    public function updateAttribute(CategoryAttribute $attribute, array $data): CategoryAttribute
    {
        return DB::transaction(function () use ($attribute, $data) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name'], '_');
            }

            $data['is_required'] = isset($data['is_required']) ? (bool) $data['is_required'] : false;
            $data['is_filterable'] = isset($data['is_filterable']) ? (bool) $data['is_filterable'] : false;
            $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $options = $data['options'] ?? null;
            unset($data['options']);

            $attribute->update($data);

            if ($attribute->type === 'select') {
                if ($options !== null) {
                    $this->saveAttributeOptions($attribute, $options);
                }
            } else {
                $attribute->options()->delete();
            }

            return $attribute;
        });
    }

    /**
     * Delete an attribute.
     */
    public function deleteAttribute(CategoryAttribute $attribute): bool
    {
        return DB::transaction(function () use ($attribute) {
            $attribute->options()->delete();
            return $attribute->delete();
        });
    }

    /**
     * Toggle Attribute Active Status.
     */
    public function toggleAttributeStatus(CategoryAttribute $attribute): bool
    {
        $attribute->is_active = !$attribute->is_active;
        $attribute->save();

        return $attribute->is_active;
    }

    /**
     * Save / Sync options for select attributes.
     */
    public function saveAttributeOptions(CategoryAttribute $attribute, array $options): void
    {
        $attribute->options()->delete();

        $sortOrder = 0;
        foreach ($options as $optionText) {
            $optionText = trim((string) $optionText);
            if ($optionText !== '') {
                AttributeOption::create([
                    'category_attribute_id' => $attribute->id,
                    'label' => $optionText,
                    'value' => Str::slug($optionText, '_'),
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }
    }
}
