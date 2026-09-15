<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    /**
     * About Us Page
     */
    public function about()
    {
        return view('frontend.pages.about', [
            'title' => 'About Bontrouver | Canada’s Trusted Local Marketplace',
            'metaDescription' => 'Learn more about Bontrouver, Canada’s premier local marketplace connecting Canadian buyers, sellers, and communities with trust and simplicity.',
        ]);
    }

    /**
     * Member Benefits Page
     */
    public function memberBenefits()
    {
        return view('frontend.pages.member-benefits', [
            'title' => 'Member Benefits & Perks | Bontrouver Canada',
            'metaDescription' => 'Explore the exclusive advantages of joining Bontrouver: verified seller badges, smart instant messaging, favorite alerts, priority support, and free local classifieds.',
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
            'metaDescription' => 'Discover Top Ad placements, Urgent ribbons, Homepage Highlights, and Daily Bumps to get up to 10x more inquiries on Bontrouver.',
        ]);
    }

    /**
     * Community Connect Page
     */
    public function communityConnect()
    {
        return view('frontend.pages.community-connect', [
            'title' => 'Community Connect | Local Meetups & Community Hub',
            'metaDescription' => 'Discover verified safe meetup zones, community programs, charitable initiatives, and local neighborhood support across Canada.',
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
