<?php

namespace App\Http\Controllers;

use App\Enums\CompanionshipType;
use App\Http\Requests\JoinCompanionshipRequest;
use App\Http\Requests\StoreCompanionshipRequest;
use App\Models\CompanionshipRequest;
use App\Services\CompanionshipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function __construct(
        private readonly CompanionshipService $companionshipService
    ) {}

    /**
     * List all upcoming open meetups, with optional city/type filters.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['city', 'type']);
        $meetups = $this->companionshipService->getActiveRequests($filters);
        $types   = CompanionshipType::values();

        return view('frontend.community.index', compact('meetups', 'types', 'filters'));
    }

    /**
     * Show a single meetup detail page.
     */
    public function show(int $id): View
    {
        $meetup = $this->companionshipService->findForShow($id);

        return view('frontend.community.show', compact('meetup'));
    }

    /**
     * Show the form to create a new meetup.
     */
    public function create(): View
    {
        $this->authorize('create', CompanionshipRequest::class);

        return view('frontend.account.meetup-create');
    }

    /**
     * Persist a new meetup request.
     */
    public function store(StoreCompanionshipRequest $request): RedirectResponse
    {
        $meetup = $this->companionshipService->createRequest(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('community.show', $meetup->id)
            ->with('success', 'Meetup request posted successfully!');
    }

    /**
     * Send a request to join an existing meetup.
     */
    public function requestToJoin(JoinCompanionshipRequest $request, int $id): RedirectResponse
    {
        $meetup = $this->companionshipService->findForShow($id);

        $this->authorize('join', $meetup);

        $this->companionshipService->requestToJoin($meetup, $request->user());

        return back()->with('success', 'Your request to join has been sent to the host.');
    }

    /**
     * Show the form to edit an existing meetup.
     */
    public function edit(int $id): View
    {
        $meetup = $this->companionshipService->findForShow($id);
        
        $this->authorize('update', $meetup);

        return view('frontend.account.meetup-edit', compact('meetup'));
    }

    /**
     * Update an existing meetup request.
     */
    public function update(\App\Http\Requests\UpdateCompanionshipRequest $request, int $id): RedirectResponse
    {
        $meetup = $this->companionshipService->findForShow($id);
        
        $this->authorize('update', $meetup);

        $this->companionshipService->updateRequest($meetup, $request->validated());

        return redirect()
            ->route('meetups.my')
            ->with('success', 'Meetup request updated successfully!');
    }

    /**
     * Cancel/Delete an existing meetup request.
     */
    public function destroy(int $id): RedirectResponse
    {
        $meetup = $this->companionshipService->findForShow($id);
        
        $this->authorize('delete', $meetup);

        $this->companionshipService->deleteRequest($meetup);

        return redirect()
            ->route('meetups.my')
            ->with('success', 'Meetup has been successfully cancelled.');
    }
}
