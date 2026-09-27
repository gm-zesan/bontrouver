<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustUserPointsRequest;
use App\Http\Requests\Admin\UpdateMemberTierRequest;
use App\Http\Requests\Admin\UpdatePointRulesRequest;
use App\Models\MemberTier;
use App\Models\User;
use App\Services\AdminMemberTierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MemberTierController extends Controller
{
    public function __construct(
        private readonly AdminMemberTierService $tierService
    ) {}

    /**
     * Display the Member Tiers, Point Rules & Live Ledger dashboard.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $filters = [
                'action_type' => $request->get('action_type'),
                'type'        => $request->get('type'),
                'search'      => $request->get('search_custom'),
            ];

            $query = $this->tierService->getPointLedgerQuery($filters);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    $user = $row->user;
                    if (!$user) {
                        return '<span class="text-muted small">Deleted User</span>';
                    }

                    $avatarUrl = $user->avatar_url;
                    $tier = $user->member_tier;
                    $tierName = e($tier['name'] ?? 'Member');

                    return '<div class="d-flex align-items-center gap-2.5">'
                        . '<img src="' . e($avatarUrl) . '" alt="' . e($user->name) . '" class="rounded-circle border" style="width: 34px; height: 34px; object-fit: cover; flex-shrink: 0;">'
                        . '<div class="d-flex flex-column">'
                        . '<div class="d-flex align-items-center gap-1.5">'
                        . '<span class="fw-bold text-dark" style="font-size: 13.5px;">' . e($user->name) . '</span>'
                        . '<span class="badge bg-light text-dark border ms-1" style="font-size: 10px;">' . $tierName . '</span>'
                        . '</div>'
                        . '<span class="text-muted small" style="font-size: 11px;">' . e($user->email) . '</span>'
                        . '</div>'
                        . '</div>';
                })
                ->editColumn('points', function ($row) {
                    $pts = (int) $row->points;
                    if ($pts > 0) {
                        return '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 12px; font-weight: 700;">'
                            . '<i class="ri-arrow-up-line me-0.5"></i>+' . number_format($pts) . ' pts'
                            . '</span>';
                    } elseif ($pts < 0) {
                        return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1" style="font-size: 12px; font-weight: 700;">'
                            . '<i class="ri-arrow-down-line me-0.5"></i>' . number_format($pts) . ' pts'
                            . '</span>';
                    }
                    return '<span class="badge bg-light text-muted border">0 pts</span>';
                })
                ->editColumn('action_type', function ($row) {
                    $action = $row->action_type;
                    $clean = ucwords(str_replace('_', ' ', $action));

                    $badgeClass = match ($action) {
                        'identity_verification', 'verified_identity' => 'bg-info-subtle text-info border-info-subtle',
                        'verified_dealer'                           => 'bg-primary-subtle text-primary border-primary-subtle',
                        'positive_review', 'positive_reviews'       => 'bg-warning-subtle text-warning border-warning-subtle',
                        'free_listing', 'giving_away_item'          => 'bg-success-subtle text-success border-success-subtle',
                        'meetup_host', 'companionship_host'         => 'bg-purple-subtle text-purple border-purple-subtle',
                        'admin_award'                               => 'bg-success-subtle text-success border-success-subtle',
                        'admin_deduct'                              => 'bg-danger-subtle text-danger border-danger-subtle',
                        'featured_promotion', 'sponsored_promotion' => 'bg-danger-subtle text-danger border-danger-subtle',
                        default                                     => 'bg-light text-dark border',
                    };

                    return '<span class="badge border ' . $badgeClass . ' fw-medium" style="font-size: 11px;">'
                        . e($clean)
                        . '</span>';
                })
                ->editColumn('description', function ($row) {
                    return '<span class="text-dark small" style="font-size: 12.5px;">' . e($row->description ?: '—') . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '<div class="d-flex flex-column">'
                        . '<span class="text-dark small fw-medium" style="font-size: 12px;">' . $row->created_at->format('M d, Y') . '</span>'
                        . '<span class="text-muted font-monospace" style="font-size: 10.5px;">' . $row->created_at->format('h:i A') . '</span>'
                        . '</div>';
                })
                ->rawColumns(['user', 'points', 'action_type', 'description', 'created_at'])
                ->make(true);
        }

        $tiers = $this->tierService->getTiersWithStats();
        $rules = $this->tierService->getPointRules();
        $kpis  = $this->tierService->getPointsKpis();
        $users = User::select('id', 'name', 'email', 'avatar', 'community_points')->orderBy('name')->get();

        return view('admin.member-tiers.index', compact('tiers', 'rules', 'kpis', 'users'));
    }

    /**
     * Get specific tier JSON for edit modal.
     */
    public function show(MemberTier $memberTier): JsonResponse
    {
        return response()->json([
            'success' => true,
            'tier'    => $memberTier,
        ]);
    }

    /**
     * Update tier thresholds and perks.
     */
    public function update(UpdateMemberTierRequest $request, MemberTier $memberTier): JsonResponse
    {
        $updated = $this->tierService->updateTier($memberTier, $request->validated());

        return response()->json([
            'success' => true,
            'message' => "Tier '{$updated->name}' thresholds updated successfully.",
            'tier'    => $updated,
        ]);
    }

    /**
     * Perform administrative point adjustment.
     */
    public function adjustPoints(AdjustUserPointsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::findOrFail($validated['user_id']);
        $amount = $validated['type'] === 'deduct' ? -abs((int) $validated['amount']) : abs((int) $validated['amount']);
        $actionType = (!empty($validated['action_type'])) ? $validated['action_type'] : ($validated['type'] === 'deduct' ? 'admin_deduct' : 'admin_award');

        $transaction = $this->tierService->adjustUserPoints(
            $user,
            $amount,
            $actionType,
            $validated['reason'],
            auth()->id()
        );

        $actionText = $amount >= 0 ? "Awarded +{$amount} points to" : "Deducted {$amount} points from";

        return response()->json([
            'success'     => true,
            'message'     => "{$actionText} {$user->name}. New balance: {$user->fresh()->community_points} pts.",
            'new_points'  => $user->fresh()->community_points,
            'member_tier' => $user->fresh()->member_tier,
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update configurable point rules.
     */
    public function updateRules(UpdatePointRulesRequest $request): JsonResponse
    {
        $rules = $this->tierService->updatePointRules($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Point reward and spending rules updated successfully.',
            'rules'   => $rules,
        ]);
    }
}
