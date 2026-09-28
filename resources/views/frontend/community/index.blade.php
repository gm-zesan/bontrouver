@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl text-center">
        <span class="static-hero-badge mx-auto">
            <i class="bi bi-people"></i> Need Companionship?
        </span>
        <h1 class="static-hero-title">Find Your Community</h1>
        <p class="static-hero-desc mx-auto">
            Join friendly, local meetups across Canada. From coffee chats to weekend hikes, connect with neighbors in a safe, welcoming environment.
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            @auth
                <a href="{{ route('community.create') }}" class="hero-btn-primary">
                    <span>Host a Meetup</span>
                    <i class="bi bi-plus-circle"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="hero-btn-primary">
                    <span>Login to Host a Meetup</span>
                </a>
            @endauth
            <a href="#meetups" class="btn-theme-outline-secondary">
                <span>Explore Events</span>
            </a>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section id="meetups" class="py-3">
    <div class="container-xl">
        <form action="{{ route('community.index') }}" method="GET" class="row g-3 align-items-center justify-content-center">
            <div class="col-md-auto">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="city" class="form-control" placeholder="City or Postal Code..." value="{{ request('city') }}">
                </div>
            </div>
            <div class="col-md-auto">
                <select name="type" class="form-select">
                    <option value="">All Activity Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="hero-btn-primary py-2 px-4" style="min-width: auto;">Filter</button>
                @if(request()->hasAny(['city', 'type']))
                    <a href="{{ route('community.index') }}" class="btn btn-link text-decoration-none ms-2">Clear</a>
                @endif
            </div>
        </form>
    </div>
</section>

<!-- Meetups Grid with Community Sidebar -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center mb-4">
            <span class="section-eyebrow">UPCOMING EVENTS</span>
            <h2 class="section-heading">Active Meetups</h2>
            <p class="section-subtext">{{ $meetups->total() }} events found based on your filters.</p>
        </div>

        <div class="row g-4">
            {{-- Main Content Column --}}
            <div class="col-lg-8">
                @if($meetups->isEmpty())
                    <div class="static-card text-center py-5">
                        <div class="display-1 text-muted mb-3 opacity-25">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h3 class="static-card-title">No meetups found</h3>
                        <p class="static-card-text mb-4">Try adjusting your filters or be the first to host an event in your area!</p>
                        <a href="{{ route('community.create') }}" class="hero-btn-primary mx-auto d-inline-flex">
                            <span>Host a Meetup</span>
                        </a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($meetups as $meetup)
                            <div class="col-md-6">
                                <div class="static-card h-100 position-relative d-flex flex-column">
                                    <!-- Status Ribbon -->
                                    @if($meetup->status == 'full')
                                        <div class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 small fw-bold">
                                            FULL
                                        </div>
                                    @elseif($meetup->meetup_date_time->isToday())
                                        <div class="position-absolute top-0 end-0 bg-success text-white px-2 py-1 small fw-bold">
                                            TODAY
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <span class="badge text-success border border-success-subtle px-2 py-1">
                                            {{ $meetup->type }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="static-card-title line-clamp-2 mb-2">
                                        <a href="{{ route('community.show', $meetup->id) }}" class="text-decoration-none" style="color: inherit;">
                                            {{ $meetup->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="static-card-text line-clamp-2 mb-4">
                                        {{ Str::limit($meetup->description, 100) }}
                                    </p>

                                    <ul class="list-unstyled mb-4 small text-secondary">
                                        <li class="mb-2">
                                            <i class="bi bi-calendar-event me-2 text-success"></i> 
                                            {{ $meetup->meetup_date_time->format('D, M j, Y • g:i A') }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-geo-alt me-2 text-success"></i> 
                                            {{ $meetup->city }}, {{ $meetup->province }}
                                        </li>
                                        <li>
                                            <i class="bi bi-wallet2 me-2 text-success"></i> 
                                            @if($meetup->expense_type == 'free')
                                                <span class="text-success fw-medium">Free</span>
                                            @elseif($meetup->expense_type == 'split')
                                                <span class="text-primary fw-medium">Split Bill</span>
                                            @else
                                                <span class="text-warning fw-medium">Host Pays</span>
                                            @endif
                                        </li>
                                    </ul>

                                    <div class="d-flex align-items-center justify-content-between pt-3 mt-auto border-top border-secondary-subtle">
                                        <div class="d-flex align-items-center">
                                            <div class="account-initials-avatar rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem; background: var(--color-primary, #49D17D); color: #fff;">
                                                {{ substr($meetup->user->name, 0, 1) }}
                                            </div>
                                            <span class="small fw-medium">{{ $meetup->user->name }}</span>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $approvedCount = $meetup->attendees->where('status', 'approved')->count();
                                            @endphp
                                            <span class="small fw-bold {{ $meetup->headcount_limit && $approvedCount >= $meetup->headcount_limit ? 'text-danger' : 'text-success' }}">
                                                {{ $approvedCount }} / {{ $meetup->headcount_limit ?? '∞' }} joined
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 text-center">
                                        <a href="{{ route('community.show', $meetup->id) }}" class="btn-theme-outline-secondary w-100 justify-content-center">
                                            <span>View Details</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        {{ $meetups->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- Community Sidebar --}}
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-4">
                    {{-- Host an Event CTA --}}
                    <div class="static-card text-center p-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-success mx-auto mb-3" style="width: 52px; height: 52px; background: rgba(73, 209, 125, 0.12); font-size: 24px;">
                            <i class="bi bi-calendar2-plus"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Host a Social Meetup</h4>
                        <p class="small text-secondary mb-3">
                            Connect with locals for coffee, sports, hiking, or gaming. Earn community aid points for hosting!
                        </p>
                        @auth
                            <a href="{{ route('community.create') }}" class="hero-btn-primary w-100 justify-content-center">
                                <span>Create a Meetup</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hero-btn-primary w-100 justify-content-center">
                                <span>Login to Host</span>
                            </a>
                        @endauth
                    </div>

                    {{-- Community Safety Guidelines --}}
                    <div class="static-card p-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success"></i> Safe Community Meetups
                        </h5>
                        <ul class="list-unstyled small text-secondary mb-0 d-flex flex-column gap-2">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check2-circle text-success mt-1"></i>
                                <span>Always meet in public, well-lit Canadian venues (cafés, parks, sports centers).</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check2-circle text-success mt-1"></i>
                                <span>Check the host's <strong>Verified Badge</strong> and Community points.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check2-circle text-success mt-1"></i>
                                <span>Never share sensitive financial details or wire money in advance.</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Sponsored Banner Ads (community_sidebar) --}}
                    @php
                        $communityBanners = \App\Models\BannerAd::active()->forPosition('community_sidebar')->orderBy('sort_order')->take(3)->get();
                    @endphp
                    @if($communityBanners->isNotEmpty())
                        <div class="d-flex flex-column gap-3">
                            @foreach($communityBanners as $banner)
                                @php $banner->recordImpression(); @endphp
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                                    <div class="px-3 py-1.5 bg-light border-bottom d-flex align-items-center justify-content-between">
                                        <span class="text-uppercase text-muted fw-bold" style="font-size: 9.5px; letter-spacing: 0.5px;">Sponsored Partner</span>
                                        <i class="bi bi-info-circle text-muted" style="font-size: 11px;" title="Verified Canadian Sponsor"></i>
                                    </div>
                                    @if(!empty($banner->html_code))
                                        <div class="p-3">
                                            {!! $banner->html_code !!}
                                        </div>
                                    @elseif(!empty($banner->image_url))
                                        <a href="{{ $banner->target_url ?? '#' }}" target="_blank" rel="noopener sponsored" class="d-block text-decoration-none">
                                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-100 object-fit-cover" style="max-height: 220px;">
                                            <div class="p-3">
                                                <div class="fw-semibold text-dark small mb-1">{{ $banner->title }}</div>
                                                @if($banner->target_url)
                                                    <span class="text-primary small fw-medium" style="font-size: 11.5px;">
                                                        Learn More <i class="bi bi-arrow-right ms-1"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
