<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Listing;
use App\Models\CompanionshipRequest;
use App\Models\UserVerification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'total_listings' => Listing::count(),
            'total_meetups' => CompanionshipRequest::count(),
            'pending_verifications' => UserVerification::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
