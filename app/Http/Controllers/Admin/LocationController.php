<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Http\Requests\Admin\UpdateProvinceRequest;
use App\Models\City;
use App\Models\Province;
use App\Services\AdminLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function __construct(
        private readonly AdminLocationService $locationService
    ) {}

    /**
     * Display Canadian locations and cities management hub.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $provinceId = $request->get('province_id') ? (int) $request->get('province_id') : null;
            $search = is_array($request->get('search')) ? ($request->get('search')['value'] ?? null) : $request->get('search');
            $status = $request->get('status');
            $featured = $request->get('featured');

            $query = $this->locationService->getCitiesQuery($provinceId, $search, $status, $featured);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    $provCode = $row->province ? $row->province->code : 'CA';
                    $provName = $row->province ? $row->province->name : 'Canada';
                    return '<div class="d-flex flex-column">'
                        . '<span class="fw-bold text-dark" style="font-size: 13.5px;">' . e($row->name) . '</span>'
                        . '<div class="d-flex align-items-center gap-1 mt-0.5">'
                        . '<span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10px;">' . e($provCode) . '</span>'
                        . '<span class="text-muted small">' . e($provName) . '</span>'
                        . '<span class="text-muted small font-monospace">/' . e($row->slug) . '</span>'
                        . '</div>'
                        . '</div>';
                })
                ->addColumn('coordinates', function ($row) {
                    $coords = number_format((float) $row->latitude, 4) . ', ' . number_format((float) $row->longitude, 4);
                    $mapUrl = 'https://www.google.com/maps?q=' . $row->latitude . ',' . $row->longitude;
                    return '<div class="d-flex flex-column">'
                        . '<span class="small font-monospace text-dark">' . $coords . '</span>'
                        . '<a href="' . $mapUrl . '" target="_blank" class="text-primary small text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 11px;">'
                        . '<i class="ri-map-pin-line"></i> View Map'
                        . '</a>'
                        . '</div>';
                })
                ->editColumn('population', function ($row) {
                    return $row->population ? '<span class="small text-dark font-monospace">' . number_format($row->population) . '</span>' : '<span class="text-muted small">—</span>';
                })
                ->addColumn('inventory', function ($row) {
                    $badgeClass = $row->listings_count > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border';
                    return '<span class="badge ' . $badgeClass . '" style="font-size: 11px;">'
                        . number_format($row->listings_count) . ' Ads</span>';
                })
                ->editColumn('is_active', function ($row) {
                    $badgeClass = $row->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle';
                    $label = $row->is_active ? 'Active' : 'Inactive';
                    return '<button type="button" class="btn p-0 border-0" onclick="toggleCityActive(' . $row->id . ')" title="Toggle Status">'
                        . '<span id="badge-active-' . $row->id . '" class="badge ' . $badgeClass . '" style="font-size: 11px; padding: 4px 8px;">' . $label . '</span>'
                        . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $cityName = addslashes($row->name);
                    return '<div class="d-inline-flex align-items-center gap-1">'
                        . '<button type="button" class="btn btn-sm btn-light border px-2 py-1" onclick="openEditCityModal(' . $row->id . ')" title="Edit City" style="height: 30px;">'
                        . '<i class="ri-edit-line text-primary"></i>'
                        . '</button>'
                        . '<button type="button" class="btn btn-sm btn-light border px-2 py-1" onclick="confirmDeleteCity(' . $row->id . ', \'' . $cityName . '\', ' . $row->listings_count . ')" title="Delete City" style="height: 30px;">'
                        . '<i class="ri-delete-bin-line text-danger"></i>'
                        . '</button>'
                        . '</div>';
                })
                ->rawColumns(['name', 'coordinates', 'population', 'inventory', 'is_active', 'action'])
                ->make(true);
        }

        $provinces = $this->locationService->getProvincesList();

        return view('admin.locations.index', compact('provinces'));
    }

    /**
     * Store a newly created Canadian city.
     */
    public function storeCity(StoreCityRequest $request): JsonResponse|RedirectResponse
    {
        $city = $this->locationService->createCity($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "City '{$city->name}' was created successfully.",
                'city'    => $city->load('province'),
            ]);
        }

        return redirect()->route('admin.locations.index')->with('success', "City '{$city->name}' created successfully.");
    }

    /**
     * Show city JSON details for modal editing.
     */
    public function showCity(City $city): JsonResponse
    {
        return response()->json([
            'success' => true,
            'city'    => $city->load('province'),
        ]);
    }

    /**
     * Update an existing Canadian city.
     */
    public function updateCity(UpdateCityRequest $request, City $city): JsonResponse|RedirectResponse
    {
        $updatedCity = $this->locationService->updateCity($city, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "City '{$updatedCity->name}' was updated successfully.",
                'city'    => $updatedCity->load('province'),
            ]);
        }

        return redirect()->route('admin.locations.index')->with('success', "City '{$updatedCity->name}' updated successfully.");
    }

    /**
     * Toggle city active status.
     */
    public function toggleCityActive(City $city): JsonResponse
    {
        $isActive = $this->locationService->toggleCityActive($city);

        return response()->json([
            'success'   => true,
            'is_active' => $isActive,
            'message'   => "City '{$city->name}' is now " . ($isActive ? 'Active' : 'Inactive') . '.',
        ]);
    }

    /**
     * Toggle city featured status.
     */
    public function toggleCityFeatured(City $city): JsonResponse
    {
        $isFeatured = $this->locationService->toggleCityFeatured($city);

        return response()->json([
            'success'     => true,
            'is_featured' => $isFeatured,
            'message'     => "City '{$city->name}' featured status updated.",
        ]);
    }

    /**
     * Remove a city with safety checks.
     */
    public function destroyCity(City $city): JsonResponse|RedirectResponse
    {
        $result = $this->locationService->deleteCity($city);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('admin.locations.index')->with('success', $result['message']);
    }

    /**
     * Show province details for editing.
     */
    public function showProvince(Province $province): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'province' => $province->loadCount(['cities', 'listings']),
        ]);
    }

    /**
     * Update a Canadian province.
     */
    public function updateProvince(UpdateProvinceRequest $request, Province $province): JsonResponse|RedirectResponse
    {
        $updated = $this->locationService->updateProvince($province, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => "Province '{$updated->name}' was updated successfully.",
                'province' => $updated,
            ]);
        }

        return redirect()->route('admin.locations.index', ['tab' => 'provinces'])->with('success', "Province '{$updated->name}' updated successfully.");
    }

    /**
     * Toggle province active status.
     */
    public function toggleProvinceActive(Province $province): JsonResponse
    {
        $isActive = $this->locationService->toggleProvinceActive($province);

        return response()->json([
            'success'   => true,
            'is_active' => $isActive,
            'message'   => "Province '{$province->name}' is now " . ($isActive ? 'Active' : 'Inactive') . '.',
        ]);
    }
}
