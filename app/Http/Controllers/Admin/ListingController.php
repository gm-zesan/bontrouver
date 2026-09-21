<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Services\AdminListingService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ListingController extends Controller
{
    public function __construct(
        protected AdminListingService $listingService
    ) {}

    /**
     * Display a listing of marketplace listings for administrators.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'status' => $request->get('status'),
                'category_id' => $request->get('category_id'),
                'featured' => $request->get('featured'),
                'sponsored' => $request->get('sponsored'),
            ];

            $query = $this->listingService->getListingsQuery($filters);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check m-0"><input class="form-check-input listing-checkbox border-secondary" type="checkbox" value="' . $row->id . '"></div>';
                })
                ->editColumn('title', function ($row) {
                    $imgUrl = $row->primaryImage 
                        ? (str_starts_with($row->primaryImage->image_path, 'http') ? $row->primaryImage->image_path : asset('storage/' . $row->primaryImage->image_path))
                        : asset('frontend/images/placeholder.jpg');

                    $title = e($row->title);
                    $showUrl = route('admin.listings.show', $row->id);

                    $html = '<div class="d-flex align-items-center">';
                    $html .= '<img src="' . $imgUrl . '" class="rounded-2 me-2 object-fit-cover shadow-sm" style="width: 44px; height: 44px;" onerror="this.src=\'https://placehold.co/80x80?text=Ad\'">';
                    $html .= '<div class="d-flex flex-column">';
                    $html .= '<a href="' . $showUrl . '" class="fw-semibold text-dark text-decoration-none" style="font-size: 13.5px;">' . $title . '</a>';
                    $html .= '<span class="text-muted small">' . e($row->city ?? 'Canada') . ($row->province ? ', ' . e($row->province) : '') . '</span>';
                    $html .= '</div></div>';

                    return $html;
                })
                ->addColumn('seller', function ($row) {
                    if (!$row->user) {
                        return '<span class="text-muted small">N/A</span>';
                    }
                    $name = e($row->user->name);
                    $profileUrl = route('admin.users.show', $row->user->id);
                    $verifiedBadge = $row->user->is_verified ? '<i class="ri-verified-badge-fill text-primary ms-1" style="font-size: 14px;" title="Verified User"></i>' : '';
                    return '<a href="' . $profileUrl . '" class="text-decoration-none fw-medium text-dark" style="font-size: 13px;">' . $name . '</a>' . $verifiedBadge;
                })
                ->editColumn('category', function ($row) {
                    return '<span class="badge bg-light text-secondary border fw-medium" style="font-size: 11.5px; padding: 4px 8px;">' . e($row->category->name ?? 'Uncategorized') . '</span>';
                })
                ->editColumn('price', function ($row) {
                    if ($row->price_type === 'free') {
                        return '<span class="badge bg-success-subtle text-success fw-bold">FREE</span>';
                    }
                    if ($row->price_type === 'contact') {
                        return '<span class="text-muted small">Contact</span>';
                    }
                    return '<span class="fw-semibold text-dark" style="font-size: 13.5px;">$' . number_format($row->price, 2) . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $statusEnum = $row->status instanceof ListingStatus ? $row->status : (ListingStatus::tryFrom($row->status) ?? ListingStatus::ACTIVE);
                    $badges = '<span class="badge ' . $statusEnum->badgeClass() . '" style="font-size: 11px; padding: 4px 8px;">' . $statusEnum->label() . '</span>';

                    if ($row->is_featured) {
                        $badges .= ' <span class="badge bg-warning text-dark ms-1" style="font-size: 10px; padding: 2px 5px;"><i class="ri-star-fill"></i> Featured</span>';
                    }
                    if ($row->is_sponsored) {
                        $badges .= ' <span class="badge bg-primary text-white ms-1" style="font-size: 10px; padding: 2px 5px;"><i class="ri-flashlight-fill"></i> Sponsored</span>';
                    }

                    return $badges;
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    $showUrl = route('admin.listings.show', $id);
                    $deleteUrl = route('admin.listings.destroy', $id);
                    $toggleFeaturedUrl = route('admin.listings.toggleFeatured', $id);
                    $toggleStatusUrl = route('admin.listings.toggleStatus', $id);

                    $statusEnum = $row->status instanceof ListingStatus ? $row->status : (ListingStatus::tryFrom($row->status) ?? ListingStatus::ACTIVE);
                    $isPaused = in_array($statusEnum, [ListingStatus::PAUSED, ListingStatus::REJECTED]);
                    $statusIcon = $isPaused ? 'ri-play-circle-line text-success' : 'ri-pause-circle-line text-warning';
                    $statusTitle = $isPaused ? 'Activate Listing' : 'Pause / Take Down';

                    $btn = '<div class="action-btn d-flex align-items-center justify-content-end gap-1">';

                    // View / Inspect button
                    $btn .= '<a href="' . $showUrl . '" class="btn btn-sm btn-light border" title="Inspect Listing" style="padding: 4px 8px; background: #fff;"><i class="ri-eye-line text-primary"></i></a>';

                    // Toggle Featured button (Filled yellow star if featured, outline star if not)
                    $isFeatured = (bool) $row->is_featured;
                    $featuredIcon = $isFeatured ? 'ri-star-fill text-warning' : 'ri-star-line text-muted';
                    $featuredTitle = $isFeatured ? 'Unfeature Listing' : 'Feature Listing';
                    $featuredDesc = $isFeatured ? 'Are you sure you want to remove the featured badge from this listing?' : 'Are you sure you want to promote and mark this listing as featured?';

                    $btn .= '<button type="button" class="btn btn-sm btn-light border btn-confirm-modal" data-action="' . $toggleFeaturedUrl . '" data-method="POST" data-title="' . $featuredTitle . '" data-desc="' . $featuredDesc . '" data-btn-class="btn-warning" data-btn-text="Confirm" title="' . $featuredTitle . '" style="padding: 4px 8px; background: #fff;"><i class="' . $featuredIcon . '"></i></button>';

                    // Pause / Activate toggle
                    $btn .= '<button type="button" class="btn btn-sm btn-light border btn-confirm-modal" data-action="' . $toggleStatusUrl . '" data-method="POST" data-title="' . $statusTitle . '" data-desc="Are you sure you want to ' . ($isPaused ? 'activate' : 'pause') . ' this listing?" data-btn-class="' . ($isPaused ? 'btn-success' : 'btn-warning') . '" data-btn-text="Confirm" title="' . $statusTitle . '" style="padding: 4px 8px; background: #fff;"><i class="' . $statusIcon . '"></i></button>';

                    // Delete button
                    $btn .= '<button type="button" class="btn btn-sm btn-light border btn-confirm-modal" data-action="' . $deleteUrl . '" data-method="DELETE" data-title="Delete Listing" data-desc="Are you absolutely sure you want to permanently delete this listing? This action cannot be undone." data-btn-class="btn-danger" data-btn-text="Delete" title="Delete" style="padding: 4px 8px; background: #fff;"><i class="ri-delete-bin-line text-danger"></i></button>';

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['checkbox', 'title', 'seller', 'category', 'price', 'status', 'action'])
                ->make(true);
        }

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.listings.index', [
            'categories' => $categories,
            'statuses' => ListingStatus::cases(),
            'counts' => [
                'all' => Listing::count(),
                'active' => Listing::where('status', ListingStatus::ACTIVE)->count(),
                'paused' => Listing::whereIn('status', [ListingStatus::PAUSED, ListingStatus::REJECTED])->count(),
                'featured' => Listing::where('is_featured', true)->count(),
            ],
        ]);
    }

    /**
     * Display a specific listing for administrative inspection and moderation.
     */
    public function show(Listing $listing)
    {
        $listing->load([
            'user',
            'category.parent',
            'city.province',
            'images',
            'attributes.categoryAttribute',
            'conversations.buyer',
            'conversations.messages',
        ]);

        return view('admin.listings.show', [
            'listing' => $listing,
        ]);
    }

    /**
     * Toggle the status between active and paused.
     */
    public function toggleStatus(Listing $listing)
    {
        $newStatus = in_array($listing->status, [ListingStatus::PAUSED, ListingStatus::REJECTED])
            ? ListingStatus::ACTIVE
            : ListingStatus::PAUSED;

        $this->listingService->updateStatus($listing, $newStatus);

        return redirect()->back()->with('status', "Listing status updated to {$newStatus->label()}.");
    }

    /**
     * Toggle featured status on a listing.
     */
    public function toggleFeatured(Listing $listing)
    {
        $updated = $this->listingService->toggleFeatured($listing);
        $state = $updated->is_featured ? 'featured' : 'unfeatured';

        return redirect()->back()->with('status', "Listing is now {$state}.");
    }

    /**
     * Toggle sponsored status on a listing.
     */
    public function toggleSponsored(Listing $listing)
    {
        $updated = $this->listingService->toggleSponsored($listing);
        $state = $updated->is_sponsored ? 'sponsored' : 'unsponsored';

        return redirect()->back()->with('status', "Listing is now {$state}.");
    }

    /**
     * Delete a listing.
     */
    public function destroy(Listing $listing)
    {
        $this->listingService->deleteListing($listing);

        return redirect()->route('admin.listings.index')->with('status', 'Listing deleted successfully.');
    }

    /**
     * Handle bulk actions for selected listings.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,pause,suspend,feature,unfeature,delete',
            'listing_ids' => 'required|array',
            'listing_ids.*' => 'integer|exists:listings,id',
        ]);

        $result = $this->listingService->handleBulkAction($request->action, $request->listing_ids);

        return response()->json($result);
    }
}
