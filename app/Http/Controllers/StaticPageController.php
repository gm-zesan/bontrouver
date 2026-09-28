<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\MemberTier;
use App\Models\PointRule;
use App\Models\PointTransaction;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    /**
     * About Us Page with dynamic Canadian platform statistics.
     */
    public function about()
    {
        $stats = [
            'provinces_count'     => Province::where('is_active', true)->count(),
            'cities_count'        => City::where('is_active', true)->count(),
            'listings_count'      => Listing::where('status', 'active')->count(),
            'verified_users_count'=> User::where('is_verified', true)->count(),
            'total_users'         => User::count(),
            'points_circulated'   => User::sum('community_points'),
        ];

        return view('frontend.pages.about', [
            'title'           => 'About Bontrouver | Canada’s Trusted Local Marketplace',
            'metaDescription' => 'Learn more about Bontrouver, Canada’s premier local marketplace connecting Canadian buyers, sellers, and communities with trust and simplicity.',
            'stats'           => $stats,
        ]);
    }

    /**
     * Member Benefits & Reputation Tiers Page (100% Dynamic).
     */
    public function memberBenefits()
    {
        $tiers = MemberTier::orderBy('min_points', 'asc')->get();
        $rules = [
            'earn'  => PointRule::where('type', 'earn')->where('is_active', true)->orderBy('sort_order')->get(),
            'spend' => PointRule::where('type', 'spend')->where('is_active', true)->orderBy('sort_order')->get(),
        ];

        $platformStats = [
            'total_members'       => User::count(),
            'verified_members'    => User::where('is_verified', true)->count(),
            'points_circulated'   => User::sum('community_points'),
            'total_transactions'  => PointTransaction::count(),
            'active_listings'     => Listing::where('status', 'active')->count(),
            'active_meetups'      => CompanionshipRequest::where('status', 'open')->count(),
            'elite_members'       => User::where('community_points', '>=', 300)->count(),
        ];

        return view('frontend.pages.member-benefits', [
            'title'           => 'Member Benefits & Tiers | Bontrouver Canada',
            'metaDescription' => 'Explore the exclusive advantages of joining Bontrouver: member tiers, mutual aid reputation points, verified badges, and free local classifieds.',
            'tiers'           => $tiers,
            'rules'           => $rules,
            'stats'           => $platformStats,
        ]);
    }

    /**
     * Terms of Use Page
     */
    public function terms()
    {
        return view('frontend.pages.terms', [
            'title' => 'Terms of Use & Service Agreement | Bontrouver',
            'metaDescription' => 'Read Bontrouver’s Terms of Use governing marketplace access, transaction safety, member accounts, and Canadian commercial policies.',
        ]);
    }

    /**
     * Privacy Policy Page
     */
    public function privacy()
    {
        return view('frontend.pages.privacy', [
            'title' => 'Privacy Policy & Data Protection | Bontrouver Canada',
            'metaDescription' => 'Understand how Bontrouver collects, protects, and handles personal information in full compliance with Canadian privacy legislation (PIPEDA).',
        ]);
    }

    /**
     * Posting Policy Page
     */
    public function postingPolicy()
    {
        return view('frontend.pages.posting-policy', [
            'title' => 'Listing & Posting Guidelines | Bontrouver',
            'metaDescription' => 'Review what items and services are permitted on Bontrouver, prohibited goods, fair pricing guidelines, and advertising rules.',
        ]);
    }

    /**
     * Trust & Security Page
     */
    public function security()
    {
        return view('frontend.pages.security', [
            'title' => 'Trust & Security Center | Safe Buying & Selling in Canada',
            'metaDescription' => 'Practical tips, safe meetup advice, fraud prevention guides, and verification standards to keep your transactions safe on Bontrouver.',
        ]);
    }

    /**
     * Verification Guide Page
     */
    public function verification()
    {
        return view('frontend.pages.verification', [
            'title' => 'Account & Seller Verification | Bontrouver Trust System',
            'metaDescription' => 'Learn how Canadian identity verification, phone confirmation, and dealer certifications build authentic trust on Bontrouver.',
        ]);
    }

    /**
     * Advertise on Bontrouver Page
     */
    public function advertise()
    {
        return view('frontend.pages.advertise', [
            'title' => 'Advertise on Bontrouver | Connect with Canadian Shoppers',
            'metaDescription' => 'Promote your local business or featured brands to millions of active Canadian shoppers looking to buy in their local communities.',
        ]);
    }

    /**
     * Tools to Promote Ads Page
     */
    public function promoteTools()
    {
        return view('frontend.pages.promote-tools', [
            'title' => 'Promote Your Ads | Boost Visibility & Sell Faster',
            'metaDescription' => 'Discover Sponsored Spotlight placements, Featured Highlight badges, and Instant Bumps to get up to 10x more inquiries on Bontrouver.',
        ]);
    }

    /**
     * Community Connect Page
     */
    public function communityConnect()
    {
        $freeCount = Listing::where('status', 'active')
            ->where(function ($q) {
                $q->where('price_type', 'free')->orWhere('price', 0);
            })->count();

        $openMeetupsCount = CompanionshipRequest::where('status', 'open')->count();

        return view('frontend.pages.community-connect', [
            'title'            => 'Community Connect | Local Meetups & Community Hub',
            'metaDescription'  => 'Discover verified safe meetup zones, community programs, charitable initiatives, and local neighborhood support across Canada.',
            'freeCount'        => $freeCount,
            'openMeetupsCount' => $openMeetupsCount,
        ]);
    }

    /**
     * Accessibility Statement Page
     */
    public function accessibility()
    {
        return view('frontend.pages.accessibility', [
            'title' => 'Accessibility Statement (AODA / ACA) | Bontrouver',
            'metaDescription' => 'Our ongoing commitment to making Bontrouver inclusive and accessible to all Canadians of all abilities.',
        ]);
    }

    /**
     * Ad Choices Page
     */
    public function adChoices()
    {
        return view('frontend.pages.ad-choices', [
            'title' => 'AdChoices & Interest-Based Advertising | Bontrouver',
            'metaDescription' => 'Learn how advertising preferences and cookies work on Bontrouver and customize your personalized ad preferences.',
        ]);
    }
}
