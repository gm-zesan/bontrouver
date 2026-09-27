<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\AdminCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct(
        private readonly AdminCategoryService $categoryService
    ) {}

    /**
     * Display categories index & visual tree view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $filters = [
                'level' => $request->get('level'),
                'parent_id' => $request->get('parent_id'),
                'is_active' => $request->get('is_active'),
            ];

            $query = $this->categoryService->getCategoriesQuery($filters);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    $iconHtml = $row->icon ? '<i class="' . e($row->icon) . ' me-2 text-primary fs-6"></i>' : '<i class="ri-folder-3-line me-2 text-muted fs-6"></i>';
                    $isChild = $row->parent_id ? true : false;
                    $levelIndent = $isChild ? '<span class="text-muted ms-3 me-1">↳</span>' : '';
                    $parentBadge = $isChild ? '<span class="badge bg-light text-muted border ms-2" style="font-size: 10px;">Subcategory</span>' : '<span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2" style="font-size: 10px;">Root Category</span>';

                    return '<div class="d-flex align-items-center">'
                        . $levelIndent
                        . $iconHtml
                        . '<div class="d-flex flex-column">'
                        . '<span class="fw-semibold text-dark" style="font-size: 13.5px;">' . e($row->name) . $parentBadge . '</span>'
                        . '<span class="text-muted font-monospace" style="font-size: 11px;">/' . e($row->slug) . '</span>'
                        . '</div>'
                        . '</div>';
                })
                ->addColumn('parent_category', function ($row) {
                    if ($row->parent) {
                        $parentIcon = $row->parent->icon ? '<i class="' . e($row->parent->icon) . ' me-1 text-secondary"></i>' : '';
                        return '<span class="badge bg-light text-dark border fw-medium" style="font-size: 12px; padding: 4px 8px;">' . $parentIcon . e($row->parent->name) . '</span>';
                    }
                    return '<span class="text-muted small">— None (Main) —</span>';
                })
                ->addColumn('attributes_count', function ($row) {
                    $count = $row->attributes_count ?? 0;
                    $url = route('admin.categories.attributes.index', $row->id);
                    $btnClass = $count > 0 ? 'btn-primary' : 'btn-outline-secondary';

                    return '<a href="' . $url . '" class="btn btn-xs ' . $btnClass . ' rounded-pill px-2.5 py-0.5 fw-medium text-decoration-none" style="font-size: 11.5px;">'
                        . '<i class="ri-equalizer-line me-1"></i> ' . $count . ' Dynamic Fields'
                        . '</a>';
                })
                ->addColumn('listings_count', function ($row) {
                    $count = $row->listings_count ?? 0;
                    return '<span class="badge bg-light text-dark border fw-semibold" style="font-size: 12px;">' . number_format($count) . ' Ads</span>';
                })
                ->editColumn('sort_order', function ($row) {
                    return '<span class="text-muted font-monospace">' . $row->sort_order . '</span>';
                })
                ->editColumn('is_active', function ($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center m-0">
                        <input class="form-check-input category-status-switch" type="checkbox" role="switch" data-id="' . $row->id . '" ' . $checked . ' style="cursor: pointer;">
                    </div>';
                })
                ->addColumn('action', function ($row) {
                    $attrBtn = '<a href="' . route('admin.categories.attributes.index', $row->id) . '" class="btn btn-sm btn-light border" style="padding: 4px 8px; background: #fff;" title="Manage Custom Attributes & Schema">
                        <i class="ri-settings-5-line text-info" style="font-size: 14px;"></i>
                    </a>';

                    return '<div class="action-btn d-flex align-items-center justify-content-end gap-1">'
                        . $attrBtn
                        . '</div>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('slug', 'like', "%{$keyword}%")
                          ->orWhere('description', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['name', 'parent_category', 'attributes_count', 'listings_count', 'sort_order', 'is_active', 'action'])
                ->make(true);
        }

        $stats = $this->categoryService->getStats();
        $parents = $this->categoryService->getParentCategories();
        $tree = $this->categoryService->getTreeData();

        return view('admin.categories.index', compact('stats', 'parents', 'tree'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse|RedirectResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Category '{$category->name}' created successfully.",
                'category' => $category,
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', "Category '{$category->name}' created successfully.");
    }

    /**
     * Get category details for editing in modal.
     */
    public function show(Category $category): JsonResponse
    {
        $category->load(['parent', 'attributes.options']);

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    /**
     * Update an existing category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse|RedirectResponse
    {
        $category = $this->categoryService->updateCategory($category, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Category '{$category->name}' updated successfully.",
                'category' => $category,
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Delete a category safely.
     */
    public function destroy(Category $category): JsonResponse|RedirectResponse
    {
        $result = $this->categoryService->deleteCategory($category);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('admin.categories.index')->with('success', $result['message']);
    }

    /**
     * Toggle Category Active Status via AJAX.
     */
    public function toggleStatus(Category $category): JsonResponse
    {
        $isActive = $this->categoryService->toggleCategoryStatus($category);

        return response()->json([
            'success' => true,
            'is_active' => $isActive,
            'message' => "Category '{$category->name}' status updated to " . ($isActive ? 'Active' : 'Inactive') . '.',
        ]);
    }
}
