@extends('admin.layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Platform Overview</h4>
            <p class="text-muted mb-0">Monitor activity and manage the Bontrouver marketplace.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 fs-6">Total Users</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($stats['total_users'] ?? 0) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri-user-3-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 fs-6">Active Listings</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($stats['total_listings'] ?? 0) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri-shopping-cart-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 fs-6">Meetups</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($stats['total_meetups'] ?? 0) }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri-team-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 fs-6">ID Verifications</p>
                        <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['pending_verifications'] ?? 0) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri-shield-check-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
