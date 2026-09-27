<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryAttributeRequest;
use App\Http\Requests\Admin\UpdateCategoryAttributeRequest;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Services\AdminCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryAttributeController extends Controller
{
    public function __construct(
        private readonly AdminCategoryService $categoryService
    ) {}

    /**
     * Display the Dynamic Attribute Schema Builder for a Category.
     */
    public function index(Category $category): View
    {
        $category->load([
            'parent',
            'attributes' => function ($query) {
                $query->with('options')->orderBy('sort_order')->orderBy('name');
            },
        ]);

        return view('admin.categories.attributes', compact('category'));
    }

    /**
     * Store a new custom attribute.
     */
    public function store(StoreCategoryAttributeRequest $request, Category $category): JsonResponse|RedirectResponse
    {
        $attribute = $this->categoryService->createAttribute($category, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Custom attribute '{$attribute->name}' created successfully.",
                'attribute' => $attribute->load('options'),
            ]);
        }

        return redirect()->route('admin.categories.attributes.index', $category->id)
            ->with('success', "Custom attribute '{$attribute->name}' created successfully.");
    }

    /**
     * Show attribute JSON for editing modal.
     */
    public function show(Category $category, CategoryAttribute $attribute): JsonResponse
    {
        $attribute->load('options');

        return response()->json([
            'success' => true,
            'attribute' => $attribute,
            'options' => $attribute->options->pluck('label')->toArray(),
        ]);
    }

    /**
     * Update an attribute and its options.
     */
    public function update(UpdateCategoryAttributeRequest $request, Category $category, CategoryAttribute $attribute): JsonResponse|RedirectResponse
    {
        $attribute = $this->categoryService->updateAttribute($attribute, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Attribute '{$attribute->name}' updated successfully.",
                'attribute' => $attribute->load('options'),
            ]);
        }

        return redirect()->route('admin.categories.attributes.index', $category->id)
            ->with('success', "Attribute '{$attribute->name}' updated successfully.");
    }

    /**
     * Delete an attribute.
     */
    public function destroy(Category $category, CategoryAttribute $attribute): JsonResponse|RedirectResponse
    {
        $name = $attribute->name;
        $this->categoryService->deleteAttribute($attribute);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Attribute '{$name}' was deleted.",
            ]);
        }

        return redirect()->route('admin.categories.attributes.index', $category->id)
            ->with('success', "Attribute '{$name}' was deleted.");
    }

    /**
     * Toggle attribute active status.
     */
    public function toggleStatus(Category $category, CategoryAttribute $attribute): JsonResponse
    {
        $isActive = $this->categoryService->toggleAttributeStatus($attribute);

        return response()->json([
            'success' => true,
            'is_active' => $isActive,
            'message' => "Attribute '{$attribute->name}' is now " . ($isActive ? 'Active' : 'Inactive') . '.',
        ]);
    }
}
