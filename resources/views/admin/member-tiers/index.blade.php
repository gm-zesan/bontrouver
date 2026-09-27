@extends('admin.layouts.app')

@section('title', 'Member Tiers & Points Configuration')

@section('content')
    <div class="container-fluid my-3 px-4">

        {{-- Top KPI Metric Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase"
                                style="letter-spacing: 0.05em; font-size: 11px;">Points in Circulation</div>
                            <div class="fs-4 fw-bold text-dark mt-1">
                                {{ number_format($kpis['total_points_circulation'] ?? 0) }}</div>
                        </div>
                        <div class="rounded-3 p-2.5 bg-success-subtle text-success d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px;">
                            <i class="ri-copper-coin-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase"
                                style="letter-spacing: 0.05em; font-size: 11px;">Total Transactions</div>
                            <div class="fs-4 fw-bold text-primary mt-1">
                                {{ number_format($kpis['total_transactions'] ?? 0) }}</div>
                        </div>
                        <div class="rounded-3 p-2.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px;">
                            <i class="ri-history-line fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase"
                                style="letter-spacing: 0.05em; font-size: 11px;">Points Awarded (30d)</div>
                            <div class="fs-4 fw-bold text-info mt-1">+{{ number_format($kpis['points_awarded_30d'] ?? 0) }}
                            </div>
                        </div>
                        <div class="rounded-3 p-2.5 bg-info-subtle text-info d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px;">
                            <i class="ri-hand-heart-line fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase"
                                style="letter-spacing: 0.05em; font-size: 11px;">Elite Tier Members</div>
                            <div class="fs-4 fw-bold text-warning mt-1">
                                {{ number_format($kpis['elite_members_count'] ?? 0) }}</div>
                        </div>
                        <div class="rounded-3 p-2.5 bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px;">
                            <i class="ri-medal-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table & Tabs Container Card --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    {{-- Card Header: Title & Breadcrumbs on left, Action on right (Standard Pattern) --}}
                    <div
                        class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                        {{-- 1. Title & Breadcrumbs --}}
                        <div class="title-with-breadcrumb">
                            <div class="fw-bold text-dark fs-6 mb-1">Member Tiers & Points Configuration</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 small">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="text-decoration-none text-muted">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Member Tiers
                                        & Points</li>
                                </ol>
                            </nav>
                        </div>

                        {{-- 2. Header Actions --}}
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <button type="button" class="btn btn-primary d-flex align-items-center gap-1.5"
                                onclick="openAdjustPointsModal()" style="height: 34px; font-size: 13px;">
                                <i class="ri-add-circle-line fs-6"></i>
                                <span>Adjust User Points</span>
                            </button>
                        </div>
                    </div>

                    {{-- Navigation Sub-Tabs using standard custom-admin-tabs --}}
                    <div class="px-4 pt-3 pb-0 bg-white border-bottom">
                        <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-inline-flex gap-1 mb-3"
                            id="memberTiersTab" role="tablist"
                            style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-2 px-3 py-2 d-flex align-items-center" id="tiers-tab"
                                    data-bs-toggle="pill" data-bs-target="#tiersPane" type="button" role="tab"
                                    aria-controls="tiersPane" aria-selected="true" style="font-size: 13px;">
                                    <i class="ri-medal-line me-2 fs-6"></i> Member Tiers & Progression
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-2 px-3 py-2 d-flex align-items-center" id="rules-tab"
                                    data-bs-toggle="pill" data-bs-target="#rulesPane" type="button" role="tab"
                                    aria-controls="rulesPane" aria-selected="false" style="font-size: 13px;">
                                    <i class="ri-settings-5-line me-2 fs-6"></i> Point Reward & Spending Rules
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-2 px-3 py-2 d-flex align-items-center" id="ledger-tab"
                                    data-bs-toggle="pill" data-bs-target="#ledgerPane" type="button" role="tab"
                                    aria-controls="ledgerPane" aria-selected="false" style="font-size: 13px;">
                                    <i class="ri-file-list-3-line me-2 fs-6"></i> Points Ledger & Audit Trail
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="memberTiersTabContent">
                            {{-- ─────────────────────────────────────────────────────────────
                            TAB 1: MEMBER TIERS HIERARCHY
                            ───────────────────────────────────────────────────────────── --}}
                            <div class="tab-pane fade show active" id="tiersPane" role="tabpanel"
                                aria-labelledby="tiers-tab">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 fs-6">Member Reputation & Trust Hierarchy</h6>
                                        <span class="text-muted small" style="font-size: 12px;">Automatic tier progression
                                            based on mutual aid and community points</span>
                                    </div>
                                </div>

                                {{-- Tier Cards Grid --}}
                                <div class="row g-3 mb-4">
                                    @foreach($tiers as $tier)
                                        <div class="col-12 col-md-6 col-xl-3">
                                            <div class="card border rounded-3 h-100 shadow-xs"
                                                style="border-top: 3.5px solid {{ $tier['badge_color'] }} !important; background-color: #ffffff;">
                                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="badge bg-light text-dark border fw-bold"
                                                                style="font-size: 11px;">Level {{ $tier['level'] }}</span>
                                                            <button type="button"
                                                                class="btn btn-light border d-flex align-items-center gap-1 py-1 px-2"
                                                                onclick="openEditTierModal({{ $tier['id'] }})"
                                                                title="Edit Tier Thresholds"
                                                                style="height: 26px; font-size: 11.5px;">
                                                                <i class="ri-edit-line text-primary"></i> Edit
                                                            </button>
                                                        </div>

                                                        <div class="d-flex align-items-center gap-2 mb-2.5">
                                                            <span class="fs-4">{{ $tier['icon'] }}</span>
                                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">
                                                                {{ $tier['clean_name'] }}
                                                            </h6>
                                                        </div>

                                                        <div class="p-2.5 bg-light rounded-2 border mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span class="text-muted small" style="font-size: 11.5px;">Point
                                                                    Range:</span>
                                                                <span class="fw-bold text-dark small font-monospace"
                                                                    style="font-size: 12px;">
                                                                    {{ number_format($tier['min_points']) }}
                                                                    @if($tier['max_points'] !== null)
                                                                        – {{ number_format($tier['max_points']) }} pts
                                                                    @else
                                                                        + pts (No Max)
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div
                                                                class="d-flex align-items-center justify-content-between mt-1.5">
                                                                <span class="text-muted small" style="font-size: 11.5px;">Active
                                                                    Members:</span>
                                                                <span class="fw-semibold text-primary small"
                                                                    style="font-size: 12px;">
                                                                    {{ number_format($tier['users_count']) }}
                                                                    ({{ $tier['percentage'] }}%)
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <p class="text-muted small mb-3"
                                                            style="font-size: 12px; line-height: 1.5;">
                                                            {{ $tier['description'] ?: 'Tier description not set.' }}
                                                        </p>

                                                        @if(!empty($tier['perks']))
                                                            <div>
                                                                <span class="fw-semibold text-dark small d-block mb-1.5"
                                                                    style="font-size: 11.5px;">
                                                                    Tier Perks & Benefits:
                                                                </span>
                                                                <ul class="list-unstyled mb-0 d-flex flex-column gap-1">
                                                                    @foreach($tier['perks'] as $perk)
                                                                        <li class="d-flex align-items-start gap-1.5 text-muted"
                                                                            style="font-size: 11.5px;">
                                                                            <i class="ri-check-line text-success mt-0.5"></i>
                                                                            <span>{{ $perk }}</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Interactive Tier Simulator Card --}}
                                <div class="card border rounded-3 p-3.5 bg-light">
                                    <div class="row align-items-center g-3">
                                        <div class="col-lg-4">
                                            <h6 class="fw-bold text-dark mb-1 fs-6">
                                                <i class="ri-calculator-line text-primary me-1"></i> Live Tier Progression
                                                Simulator
                                            </h6>
                                            <span class="text-muted small" style="font-size: 12px;">Test how any point
                                                amount maps to member level and progress</span>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="input-group" style="height: 34px;">
                                                <span class="input-group-text bg-white text-muted border-end-0"
                                                    style="height: 34px;">
                                                    <i class="ri-copper-coin-line"></i>
                                                </span>
                                                <input type="number" id="sim_points_input"
                                                    class="form-control table-search-input border-start-0"
                                                    placeholder="Enter test points (e.g. 250)..." value="250"
                                                    oninput="simulateTierProgression(this.value)"
                                                    style="height: 34px; font-size: 13px;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div id="sim_result_box"
                                                class="p-2.5 bg-white rounded-2 border d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span id="sim_tier_icon" class="fs-4">🥈</span>
                                                    <div>
                                                        <div class="fw-bold text-dark small" id="sim_tier_name"
                                                            style="font-size: 13px;">Active Member (Level 2)</div>
                                                        <div class="text-muted" style="font-size: 11px;"
                                                            id="sim_tier_progress">75% to Trusted Member</div>
                                                    </div>
                                                </div>
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle"
                                                    id="sim_badge">Active</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ─────────────────────────────────────────────────────────────
                            TAB 2: POINT REWARD & SPENDING RULES
                            ───────────────────────────────────────────────────────────── --}}
                            <div class="tab-pane fade" id="rulesPane" role="tabpanel" aria-labelledby="rules-tab">
                                <form id="pointRulesForm" onsubmit="handleSavePointRules(event)">
                                    @csrf
                                    @method('PUT')

                                    {{-- Tab Action Header --}}
                                    <div
                                        class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3 pb-3 border-bottom">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1 fs-6">System Point Reward & Spending Rules
                                                Matrix</h5>
                                            <p class="text-muted small mb-0" style="font-size: 12.5px;">
                                                Configure the exact community points awarded for trust-building actions and
                                                deducted for promotional spotlight placements.
                                            </p>
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary d-flex align-items-center gap-2 px-3.5 shadow-sm"
                                            id="btnSaveRules" style="height: 36px; font-size: 13px; font-weight: 600;">
                                            <i class="ri-save-3-line fs-6"></i>
                                            <span>Save Configuration Changes</span>
                                        </button>
                                    </div>

                                    <div class="row g-4">
                                        {{-- Earn Rules Column --}}
                                        <div class="col-lg-6">
                                            <div class="card border rounded-3 h-100 shadow-sm overflow-hidden bg-white">
                                                <div
                                                    class="card-header bg-light border-bottom py-3 px-3.5 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center"
                                                            style="width: 32px; height: 32px;">
                                                            <i class="ri-add-circle-fill fs-5"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 13.5px;">
                                                                Point Earning Actions (Rewards)</h6>
                                                            <span class="text-muted" style="font-size: 11px;">Mutual aid
                                                                bonuses awarded to members</span>
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                                        style="font-size: 11px; font-weight: 600;">
                                                        {{ count($rules['earn']) }} Rules Active
                                                    </span>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="list-group list-group-flush">
                                                        @foreach($rules['earn'] as $earnRule)
                                                            <div class="list-group-item p-3 border-bottom d-flex align-items-center justify-content-between gap-3 flex-wrap flex-sm-nowrap"
                                                                style="transition: background-color 0.15s ease;">
                                                                <div style="flex: 1; min-width: 200px;">
                                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                                        <span class="fw-bold text-dark"
                                                                            style="font-size: 13.5px;">{{ $earnRule['name'] }}</span>
                                                                        <span
                                                                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill"
                                                                            style="font-size: 10px; font-weight: 600;">
                                                                            {{ $earnRule['category'] }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="text-muted"
                                                                        style="font-size: 12px; line-height: 1.45;">
                                                                        {{ $earnRule['description'] }}
                                                                    </div>
                                                                </div>
                                                                <div class="input-group"
                                                                    style="width: 150px; height: 36px; flex-shrink: 0;">
                                                                    <span
                                                                        class="input-group-text bg-success-subtle text-success border-success-subtle fw-bold"
                                                                        style="font-size: 14px; width: 34px; justify-content: center;">+</span>
                                                                    <input type="number"
                                                                        class="form-control text-center font-monospace fw-bold text-dark border-secondary-subtle"
                                                                        name="earn[{{ $earnRule['key'] }}]"
                                                                        value="{{ $earnRule['points'] }}" min="1" max="5000"
                                                                        style="font-size: 14px; color: #0f172a !important;">
                                                                    <span
                                                                        class="input-group-text bg-light text-secondary border-secondary-subtle font-monospace"
                                                                        style="font-size: 11.5px; font-weight: 600;">pts</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Spend Rules Column --}}
                                        <div class="col-lg-6">
                                            <div class="card border rounded-3 h-100 shadow-sm overflow-hidden bg-white">
                                                <div
                                                    class="card-header bg-light border-bottom py-3 px-3.5 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center"
                                                            style="width: 32px; height: 32px;">
                                                            <i class="ri-indeterminate-circle-fill fs-5"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 13.5px;">
                                                                Point Spending Actions (Promotions)</h6>
                                                            <span class="text-muted" style="font-size: 11px;">Points
                                                                deducted for marketplace promotion</span>
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"
                                                        style="font-size: 11px; font-weight: 600;">
                                                        {{ count($rules['spend']) }} Rules Active
                                                    </span>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="list-group list-group-flush">
                                                        @foreach($rules['spend'] as $spendRule)
                                                            <div class="list-group-item p-3 border-bottom d-flex align-items-center justify-content-between gap-3 flex-wrap flex-sm-nowrap"
                                                                style="transition: background-color 0.15s ease;">
                                                                <div style="flex: 1; min-width: 200px;">
                                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                                        <span class="fw-bold text-dark"
                                                                            style="font-size: 13.5px;">{{ $spendRule['name'] }}</span>
                                                                        <span
                                                                            class="badge bg-warning text-warning-emphasis border border-warning-subtle px-2 py-0.5 rounded-pill"
                                                                            style="font-size: 10px; font-weight: 600;">
                                                                            {{ $spendRule['category'] }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="text-muted"
                                                                        style="font-size: 12px; line-height: 1.45;">
                                                                        {{ $spendRule['description'] }}
                                                                    </div>
                                                                </div>
                                                                <div class="input-group"
                                                                    style="width: 150px; height: 36px; flex-shrink: 0;">
                                                                    <span
                                                                        class="input-group-text bg-danger-subtle text-danger border-danger-subtle fw-bold"
                                                                        style="font-size: 14px; width: 34px; justify-content: center;">-</span>
                                                                    <input type="number"
                                                                        class="form-control text-center font-monospace fw-bold text-dark border-secondary-subtle"
                                                                        name="spend[{{ $spendRule['key'] }}]"
                                                                        value="{{ $spendRule['points'] }}" min="1" max="10000"
                                                                        style="font-size: 14px; color: #0f172a !important;">
                                                                    <span
                                                                        class="input-group-text bg-light text-secondary border-secondary-subtle font-monospace"
                                                                        style="font-size: 11.5px; font-weight: 600;">pts</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- ─────────────────────────────────────────────────────────────
                            TAB 3: LIVE POINTS LEDGER & AUDIT TRAIL
                            ───────────────────────────────────────────────────────────── --}}
                            <div class="tab-pane fade" id="ledgerPane" role="tabpanel" aria-labelledby="ledger-tab">
                                {{-- 34px Unified Table Toolbar --}}
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        {{-- Search Input --}}
                                        <div class="input-group" style="width: 230px; height: 34px;">
                                            <span class="input-group-text bg-white text-muted border-end-0"
                                                style="height: 34px;">
                                                <i class="ri-search-line"></i>
                                            </span>
                                            <input type="text" id="ledger_search"
                                                class="form-control table-search-input border-start-0"
                                                placeholder="Search member, notes..."
                                                style="height: 34px; font-size: 12.5px;">
                                        </div>

                                        {{-- Action Type Filter --}}
                                        <select id="ledger_action_type" class="form-select table-filter-select"
                                            style="width: 190px; height: 34px; font-size: 12.5px;">
                                            <option value="all">All Action Types</option>
                                            <option value="identity_verification">ID Verification (+50)</option>
                                            <option value="verified_dealer">Dealer License (+100)</option>
                                            <option value="positive_review">Positive Review (+20)</option>
                                            <option value="free_listing">Free Item Donated (+25)</option>
                                            <option value="meetup_host">Meetup Host (+30)</option>
                                            <option value="admin_award">Admin Award (+)</option>
                                            <option value="admin_deduct">Admin Deduction (-)</option>
                                            <option value="featured_promotion">Featured Promotion (-)</option>
                                            <option value="sponsored_promotion">Hero Carousel (-)</option>
                                        </select>

                                        {{-- Point Direction Filter --}}
                                        <select id="ledger_type" class="form-select table-filter-select"
                                            style="width: 145px; height: 34px; font-size: 12.5px;">
                                            <option value="">All Flow (+ / -)</option>
                                            <option value="earned">Earned Points (+)</option>
                                            <option value="spent">Spent Points (-)</option>
                                        </select>

                                        <button type="button" id="btn_filter_ledger"
                                            class="btn btn-light border table-filter-btn d-flex align-items-center gap-1"
                                            style="height: 34px; font-size: 12.5px;">
                                            <i class="ri-filter-3-line"></i>
                                            <span>Filter</span>
                                        </button>
                                        <button type="button" id="btn_reset_ledger"
                                            class="btn btn-light border table-filter-btn"
                                            style="height: 34px; font-size: 12.5px;" title="Reset filters">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-primary d-flex align-items-center gap-1.5"
                                            onclick="openAdjustPointsModal()" style="height: 34px; font-size: 13px;">
                                            <i class="ri-add-circle-line fs-6"></i>
                                            <span>Manual Point Adjustment</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- DataTables Ledger Table --}}
                                <div class="table-responsive">
                                    <table class="table dataTable w-100 align-middle" id="point-ledger-table">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" style="min-width: 220px;">Member</th>
                                                <th scope="col" style="width: 120px;">Points</th>
                                                <th scope="col" style="min-width: 170px;">Action Type</th>
                                                <th scope="col" style="min-width: 250px;">Description & Reason</th>
                                                <th scope="col" style="width: 130px;">Timestamp</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Loaded via AJAX --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Member Tier Modal --}}
    <div class="modal fade" id="editTierModal" tabindex="-1" aria-labelledby="editTierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-white border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="ri-medal-line fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="editTierModalLabel">Edit Member Tier
                            </h5>
                            <span class="text-muted small" style="font-size: 12px;">Configure point thresholds, badge
                                styling, and unlocked perks</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editTierForm" onsubmit="handleTierFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="tier_id" name="id" value="">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1.5" style="font-size: 12.5px;">Tier
                                    Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tier_name" name="name" required
                                    style="height: 34px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1.5" style="font-size: 12.5px;">Icon
                                    / Emoji</label>
                                <input type="text" class="form-control text-center" id="tier_icon" name="icon"
                                    placeholder="e.g. 🥇, ⭐"
                                    style="height: 34px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1.5"
                                    style="font-size: 12.5px;">Color Code</label>
                                <div class="input-group" style="height: 34px;">
                                    <input type="color" class="form-control form-control-color p-1" id="tier_color_picker"
                                        value="#cd7f32" onchange="$('#tier_badge_color').val(this.value)"
                                        style="width: 42px; height: 34px;">
                                    <input type="text" class="form-control font-monospace" id="tier_badge_color"
                                        name="badge_color" value="#cd7f32" oninput="$('#tier_color_picker').val(this.value)"
                                        style="height: 34px; font-size: 12.5px;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1.5"
                                    style="font-size: 12.5px;">Minimum Points <span class="text-danger">*</span></label>
                                <div class="input-group" style="height: 34px;">
                                    <span class="input-group-text bg-light text-muted"
                                        style="height: 34px; font-size: 12px;">Min</span>
                                    <input type="number" class="form-control font-monospace" id="tier_min_points"
                                        name="min_points" min="0" required
                                        style="height: 34px; font-size: 13px; border: 1px solid #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted"
                                        style="height: 34px; font-size: 12px;">pts</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1.5"
                                    style="font-size: 12.5px;">Maximum Points</label>
                                <div class="input-group" style="height: 34px;">
                                    <span class="input-group-text bg-light text-muted"
                                        style="height: 34px; font-size: 12px;">Max</span>
                                    <input type="number" class="form-control font-monospace" id="tier_max_points"
                                        name="max_points" min="0" placeholder="Leave empty for unlimited (+)"
                                        style="height: 34px; font-size: 13px; border: 1px solid #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted"
                                        style="height: 34px; font-size: 12px;">pts</span>
                                </div>
                                <div class="form-text mt-1 text-muted" style="font-size: 11px;">Leave empty for the highest
                                    tier (no upper point limit).</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1.5" style="font-size: 12.5px;">Tier
                                    Description</label>
                                <textarea class="form-control" id="tier_description" name="description" rows="2"
                                    placeholder="Brief summary of member standing..."
                                    style="border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1.5" style="font-size: 12.5px;">Tier
                                    Perks (One perk per line)</label>
                                <textarea class="form-control font-monospace" id="tier_perks" name="perks" rows="4"
                                    placeholder="Priority support queue&#10;10% point discount on featured ads&#10;Special profile badge"
                                    style="border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;"></textarea>
                            </div>
                        </div>
                    </div>

                    <div
                        class="modal-footer bg-light border-top px-4 py-2.5 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal"
                            style="height: 34px; font-size: 13px;">Cancel</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1.5 px-4"
                            id="btnSaveTier" style="height: 34px; font-size: 13px;">
                            <i class="ri-save-line fs-6"></i>
                            <span>Update Tier Thresholds</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Manual Point Adjustment Modal --}}
    <div class="modal fade" id="adjustPointsModal" tabindex="-1" aria-labelledby="adjustPointsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-white border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="ri-copper-coin-line fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="adjustPointsModalLabel">Manual Point
                                Adjustment</h5>
                            <span class="text-muted small" style="font-size: 12px;">Award or deduct community reputation
                                points</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="adjustPointsForm" onsubmit="handleAdjustPointsSubmit(event)">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            {{-- User Select --}}
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1.5"
                                    style="font-size: 12.5px;">Select Member <span class="text-danger">*</span></label>
                                <select class="form-select" id="adj_user_id" name="user_id" required
                                    style="height: 34px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                    <option value="">-- Choose Member --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">
                                            {{ $u->name }} ({{ $u->email }}) — Current:
                                            {{ number_format($u->community_points) }} pts
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Action Type --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1.5"
                                    style="font-size: 12.5px;">Adjustment Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="adj_type" name="type" required
                                    style="height: 34px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                    <option value="award" selected>➕ Award Points (Add)</option>
                                    <option value="deduct">➖ Deduct Points (Subtract)</option>
                                </select>
                            </div>

                            {{-- Point Amount --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1.5"
                                    style="font-size: 12.5px;">Point Amount <span class="text-danger">*</span></label>
                                <div class="input-group" style="height: 34px;">
                                    <input type="number" class="form-control font-monospace fw-bold" id="adj_amount"
                                        name="amount" min="1" max="50000" placeholder="e.g. 50" required
                                        style="height: 34px; font-size: 13px; border: 1px solid #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted"
                                        style="height: 34px; font-size: 12px;">pts</span>
                                </div>
                            </div>

                            {{-- Reason / Description --}}
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1.5"
                                    style="font-size: 12.5px;">Reason / Internal Note <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="adj_reason" name="reason" rows="2"
                                    placeholder="e.g. Community Hero Award for organizing neighborhood charity drive..."
                                    required
                                    style="border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;"></textarea>
                                <div class="form-text mt-1 text-muted" style="font-size: 11px;">This note will appear in the
                                    member's audit ledger.</div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="modal-footer bg-light border-top px-4 py-2.5 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal"
                            style="height: 34px; font-size: 13px;">Cancel</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1.5 px-4"
                            id="btnSubmitAdjustment" style="height: 34px; font-size: 13px;">
                            <i class="ri-check-line fs-6"></i>
                            <span>Apply Adjustment</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-admin.confirm-modal />
@endsection

@push('custom-script')
    <script>
        $(document).ready(function () {
            // ─────────────────────────────────────────────────────────────
            // 1. DATA TABLES LEDGER
            // ─────────────────────────────────────────────────────────────
            var ledgerTable = $('#point-ledger-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.member-tiers.index') }}",
                    data: function (d) {
                        d.action_type = $('#ledger_action_type').val();
                        d.type = $('#ledger_type').val();
                        d.search_custom = $('#ledger_search').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center fw-medium text-muted' },
                    { data: 'user', name: 'user', orderable: false },
                    { data: 'points', name: 'points', orderable: true, className: 'text-center' },
                    { data: 'action_type', name: 'action_type', orderable: true },
                    { data: 'description', name: 'description', orderable: false },
                    { data: 'created_at', name: 'created_at', orderable: true }
                ],
                order: [[5, 'desc']],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
                language: {
                    search: "",
                    searchPlaceholder: "Search records...",
                    processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading ledger...',
                    emptyTable: "No point transactions recorded yet.",
                    paginate: {
                        previous: '<i class="ri-arrow-left-s-line"></i>',
                        next: '<i class="ri-arrow-right-s-line"></i>'
                    }
                },
                dom: '<"d-none"f>rt<"d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 border-top"lip>'
            });

            // Search and filter triggers
            $('#btn_filter_ledger').on('click', function () {
                ledgerTable.ajax.reload();
            });

            $('#ledger_action_type, #ledger_type').on('change', function () {
                ledgerTable.ajax.reload();
            });

            var searchTimeout;
            $('#ledger_search').on('keyup', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    ledgerTable.ajax.reload();
                }, 350);
            });

            $('#btn_reset_ledger').on('click', function () {
                $('#ledger_search').val('');
                $('#ledger_action_type').val('all');
                $('#ledger_type').val('');
                ledgerTable.ajax.reload();
            });

            // Tab state preservation & DataTables adjustment
            $('button[data-bs-toggle="pill"], button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("data-bs-target");
                if (target === '#ledgerPane') {
                    ledgerTable.columns.adjust().draw();
                }
            });

            // Initialize simulator
            simulateTierProgression(250);
        });

        // ─────────────────────────────────────────────────────────────
        // 2. TIER EDIT MODAL
        // ─────────────────────────────────────────────────────────────
        function openEditTierModal(tierId) {
            var url = "{{ url('admin/member-tiers') }}/" + tierId;

            $.get(url, function (res) {
                if (res.success && res.tier) {
                    var t = res.tier;
                    $('#tier_id').val(t.id);
                    $('#tier_name').val(t.name);
                    $('#tier_icon').val(t.icon || '');
                    $('#tier_badge_color').val(t.badge_color || '#cd7f32');
                    $('#tier_color_picker').val(t.badge_color || '#cd7f32');
                    $('#tier_min_points').val(t.min_points);
                    $('#tier_max_points').val(t.max_points !== null ? t.max_points : '');
                    $('#tier_description').val(t.description || '');

                    var perksText = Array.isArray(t.perks) ? t.perks.join("\n") : (t.perks || '');
                    $('#tier_perks').val(perksText);

                    $('#editTierModal').modal('show');
                }
            }).fail(function () {
                window.showToast("Failed to fetch tier details.", true, "Error");
            });
        }

        function handleTierFormSubmit(e) {
            e.preventDefault();
            var tierId = $('#tier_id').val();
            var url = "{{ url('admin/member-tiers') }}/" + tierId;
            var btn = $('#btnSaveTier');

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: url,
                type: 'PUT',
                data: $('#editTierForm').serialize(),
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="ri-save-line fs-6"></i> <span>Update Tier Thresholds</span>');
                    if (res.success) {
                        $('#editTierModal').modal('hide');
                        window.showToast(res.message || "Tier updated successfully.", false, "Success");
                        setTimeout(function () {
                            location.reload();
                        }, 800);
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="ri-save-line fs-6"></i> <span>Update Tier Thresholds</span>');
                    var msg = "Failed to update tier.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    window.showToast(msg, true, "Validation Error");
                }
            });
        }

        // ─────────────────────────────────────────────────────────────
        // 3. POINT RULES FORM
        // ─────────────────────────────────────────────────────────────
        function handleSavePointRules(e) {
            e.preventDefault();
            var btn = $('#btnSaveRules');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: "{{ route('admin.member-tiers.updateRules') }}",
                type: 'PUT',
                data: $('#pointRulesForm').serialize(),
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="ri-save-line fs-6"></i> <span>Save Configuration Changes</span>');
                    if (res.success) {
                        window.showToast(res.message || "Point rules updated successfully.", false, "Settings Saved");
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="ri-save-line fs-6"></i> <span>Save Configuration Changes</span>');
                    window.showToast("Failed to save rules.", true, "Error");
                }
            });
        }

        // ─────────────────────────────────────────────────────────────
        // 4. MANUAL POINT ADJUSTMENT
        // ─────────────────────────────────────────────────────────────
        function openAdjustPointsModal(userId) {
            $('#adjustPointsForm')[0].reset();
            if (userId) {
                $('#adj_user_id').val(userId);
            }
            $('#adjustPointsModal').modal('show');
        }

        function handleAdjustPointsSubmit(e) {
            e.preventDefault();
            var btn = $('#btnSubmitAdjustment');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Applying...');

            $.ajax({
                url: "{{ route('admin.member-tiers.adjustPoints') }}",
                type: 'POST',
                data: $('#adjustPointsForm').serialize(),
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="ri-check-line fs-6"></i> <span>Apply Adjustment</span>');
                    if (res.success) {
                        $('#adjustPointsModal').modal('hide');
                        window.showToast(res.message || "Points adjusted successfully.", false, "Adjustment Applied");
                        $('#point-ledger-table').DataTable().ajax.reload();
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="ri-check-line fs-6"></i> <span>Apply Adjustment</span>');
                    var msg = "Failed to adjust points.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    window.showToast(msg, true, "Adjustment Error");
                }
            });
        }

        // ─────────────────────────────────────────────────────────────
        // 5. LIVE SIMULATOR LOGIC
        // ─────────────────────────────────────────────────────────────
        var tiersData = @json($tiers);

        function simulateTierProgression(pts) {
            var points = parseInt(pts) || 0;
            if (points < 0) points = 0;

            var matchedTier = null;
            var nextTier = null;

            for (var i = 0; i < tiersData.length; i++) {
                var t = tiersData[i];
                var min = t.min_points;
                var max = t.max_points;

                if (points >= min && (max === null || points <= max)) {
                    matchedTier = t;
                    nextTier = tiersData[i + 1] || null;
                    break;
                }
            }

            if (!matchedTier && tiersData.length > 0) {
                matchedTier = tiersData[tiersData.length - 1];
            }

            if (matchedTier) {
                $('#sim_tier_icon').text(matchedTier.icon || '🥉');
                $('#sim_tier_name').text(matchedTier.clean_name + ' (Level ' + matchedTier.level + ')');
                $('#sim_badge').text(matchedTier.clean_name).css('background-color', matchedTier.badge_color + '22').css('color', matchedTier.badge_color);

                if (nextTier) {
                    var range = nextTier.min_points - matchedTier.min_points;
                    var current = points - matchedTier.min_points;
                    var pct = Math.min(100, Math.max(0, Math.round((current / range) * 100)));
                    var needed = nextTier.min_points - points;
                    $('#sim_tier_progress').text(pct + '% progress (requires ' + needed + ' more pts for ' + nextTier.clean_name + ')');
                } else {
                    $('#sim_tier_progress').text('⭐ Top Tier Max Level achieved!');
                }
            }
        }
    </script>
@endpush