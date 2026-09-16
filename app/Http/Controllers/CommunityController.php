<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CompanionshipService;
use App\Http\Requests\StoreCompanionshipRequest;
use App\Models\CompanionshipRequest;
use Illuminate\Support\Facades\Log;

class CommunityController extends Controller
{
    protected CompanionshipService $companionshipService;

    public function __construct(CompanionshipService $companionshipService)
    {
        $this->companionshipService = $companionshipService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['city', 'type']);
        $meetups = $this->companionshipService->getActiveRequests($filters);
        
        $types = \App\Enums\CompanionshipType::values();

        return view('frontend.community.index', compact('meetups', 'types', 'filters'));
    }

    public function show($id)
    {
        $meetup = CompanionshipRequest::with(['user', 'cityRelation', 'attendees.user'])->findOrFail($id);
        
        return view('frontend.community.show', compact('meetup'));
    }

    public function create()
    {
        return view('frontend.account.meetup-create');
    }

    public function store(StoreCompanionshipRequest $request)
    {
        try {
            $meetup = $this->companionshipService->createRequest($request->validated(), auth()->user());
            return redirect()->route('community.show', $meetup->id)->with('success', 'Meetup request posted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create meetup: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to post meetup request. Please try again.');
        }
    }

    public function requestToJoin(Request $request, $id)
    {
        try {
            $meetup = CompanionshipRequest::findOrFail($id);
            $this->companionshipService->requestToJoin($meetup, auth()->user());
            
            return back()->with('success', 'Your request to join has been sent to the host.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
