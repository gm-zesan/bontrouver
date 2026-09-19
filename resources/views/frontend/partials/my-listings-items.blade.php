@foreach($listings as $item)
    <div class="dark-surface-card listing-manage-card mb-3" 
         data-id="{{ $item['id'] }}" 
         data-status="{{ $item['status'] }}" 
         data-category="{{ $item['category'] }}" 
         data-title="{{ strtolower($item['title']) }}"
         data-price="{{ (float) filter_var($item['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) }}"
         data-views="{{ $item['views'] }}"
         data-saves="{{ $item['saves'] }}"
         style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px;">
        
        <div class="p-3 p-lg-3 px-xl-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 gap-xl-4">
                
                <!-- 1. Left: Thumbnail + Title & Details -->
                <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width: 0;">

                    <!-- Listing Thumbnail -->
                    <div class="listing-manage-thumb position-relative flex-shrink-0" style="width: 100px; height: 76px; min-width: 100px; max-width: 100px; min-height: 76px; max-height: 76px; border-radius: 10px; overflow: hidden; background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <img src="{{ $item['image'] }}" 
                             alt="{{ $item['title'] }}" 
                             class="w-100 h-100 object-fit-cover d-block"
                             style="width: 100%; height: 100%; object-fit: cover; display: block;"
                             onerror="this.src='https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?auto=format&fit=crop&w=400&q=80'">
                        @if(!empty($item['sponsored']))
                            <span class="listing-manage-featured-badge" style="background: var(--brand-purple, #6f42c1); color: white;">
                                SPONSORED
                            </span>
                        @elseif(!empty($item['featured']))
                            <span class="listing-manage-featured-badge">
                                FEATURED
                            </span>
                        @endif
                    </div>

                    <!-- Title & Meta Info -->
                    <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 px-2 py-0" style="font-size: 0.72rem;">#{{ $item['id'] }}</span>
                            <span class="small text-secondary text-truncate" style="font-size: 0.8rem;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $item['location'] }}</span>
                        </div>
                        
                        <h6 class="fw-bold mb-1 text-truncate" style="font-size: 0.95rem;">
                            @if($item['status'] === 'draft')
                                <a href="{{ url('/post-ad?draft=' . $item['id']) }}" class="text-decoration-none text-white hover-brand-green">
                                    {{ $item['title'] }}
                                </a>
                            @else
                                <a href="{{ url('/listing/' . $item['id']) }}" class="text-decoration-none text-white hover-brand-green">
                                    {{ $item['title'] }}
                                </a>
                            @endif
                        </h6>

                        <div class="small text-secondary d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.8rem;">
                            <span><i class="bi bi-clock me-1"></i>{{ $item['posted_at'] }}</span>
                            <span class="d-none d-sm-inline opacity-50">•</span>
                            <span class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 px-2 py-0">{{ $item['category'] }}</span>
                        </div>

                        <!-- Attention / Moderation alert if flagged -->
                        @if($item['status'] === 'attention')
                            <div class="mt-2 p-2 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-3 small d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-circle-fill text-warning fs-6"></i>
                                <div class="flex-grow-1" style="font-size: 0.78rem;">
                                    <strong>Needs Attention:</strong> {{ $item['attention_reason'] }}
                                </div>
                                <a href="{{ url('/post-ad?edit=' . $item['id']) }}" class="btn btn-sm btn-outline-dark py-0 px-2 fw-semibold">Review</a>
                            </div>
                        @endif

                        <!-- Draft completion bar if draft -->
                        @if($item['status'] === 'draft')
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center small text-secondary mb-1" style="font-size: 0.75rem;">
                                    <span>Draft Progress: <strong class="text-success">{{ $item['draft_progress'] }}%</strong></span>
                                    @if(!empty($item['missing_fields']))
                                        <span class="text-warning">Missing: {{ $item['missing_fields'] }}</span>
                                    @endif
                                </div>
                                <div class="progress" style="height: 6px; background: #081D33;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item['draft_progress'] }}%" aria-valuenow="{{ $item['draft_progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- 2. Right: Price + Status + Performance Stats + Direct Actions -->
                <div class="d-flex align-items-center justify-content-between justify-content-lg-end gap-3 gap-xl-4 flex-shrink-0 flex-wrap flex-lg-nowrap">
                    
                    <!-- Price & Status Badge (Stacked) -->
                    <div class="text-start text-lg-center" style="min-width: 90px;">
                        <div class="fs-5 fw-bold text-success lh-1 mb-1">{{ $item['price'] }}</div>
                        <div>
                            @if($item['status'] === 'active')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 my-listing-badge">
                                    <i class="bi bi-broadcast"></i> Active
                                </span>
                            @elseif($item['status'] === 'draft')
                                <span class="badge bg-secondary bg-opacity-10 text-white-50 border border-secondary border-opacity-25 my-listing-badge">
                                    <i class="bi bi-pencil-fill"></i> Draft
                                </span>
                            @elseif($item['status'] === 'sold')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 my-listing-badge">
                                    <i class="bi bi-check-circle-fill"></i> Sold
                                </span>
                            @elseif($item['status'] === 'expired')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 my-listing-badge">
                                    <i class="bi bi-hourglass-bottom"></i> Expired
                                </span>
                            @elseif($item['status'] === 'paused')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 my-listing-badge">
                                    <i class="bi bi-pause-circle"></i> Paused
                                </span>
                            @elseif($item['status'] === 'attention')
                                <span class="badge bg-warning text-dark my-listing-badge">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Review
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Performance Stats Widget (Clean pill container) -->
                    <div class="px-3 py-2 rounded-3" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08);">
                        @if($item['status'] === 'draft')
                            <span class="text-secondary small fst-italic" style="font-size: 0.78rem;">Draft</span>
                        @else
                            <div class="d-flex align-items-center gap-3 text-secondary" style="font-size: 0.82rem;">
                                <div class="text-center" title="{{ $item['views'] }} Total Views" style="min-width: 28px;">
                                    <i class="bi bi-eye text-primary d-block mb-1" style="font-size: 0.95rem;"></i>
                                    <span class="text-white fw-bold" style="font-size: 0.8rem;">{{ $item['views'] }}</span>
                                </div>
                                <div class="text-center" title="{{ $item['saves'] }} Buyer Saves" style="min-width: 28px;">
                                    <i class="bi bi-heart text-danger d-block mb-1" style="font-size: 0.95rem;"></i>
                                    <span class="text-white fw-bold" style="font-size: 0.8rem;">{{ $item['saves'] }}</span>
                                </div>
                                <div class="text-center" title="{{ $item['messages'] }} Buyer Inquiries" style="min-width: 28px;">
                                    <i class="bi bi-chat-left-text text-warning d-block mb-1" style="font-size: 0.95rem;"></i>
                                    <span class="text-white fw-bold" style="font-size: 0.8rem;">{{ $item['messages'] }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Direct Single-Line Action Buttons -->
                    <div class="d-flex align-items-center gap-2 flex-nowrap">
                        
                        @if($item['status'] === 'active' || $item['status'] === 'attention')
                            <!-- Edit Button -->
                            <a href="{{ url('/post-ad?edit=' . $item['id']) }}" class="btn-manage-edit" title="Edit Listing">
                                <i class="bi bi-pencil"></i>
                                <span>Edit</span>
                            </a>
                            <!-- Promote Button -->
                            @if(empty($item['featured']) && empty($item['sponsored']))
                                <button type="button" class="btn-manage-icon icon-promote" onclick="openPromoteModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}')" title="Promote Listing">
                                    <i class="bi bi-rocket-takeoff text-info"></i>
                                </button>
                            @endif
                            <!-- Pause Button -->
                            <button type="button" class="btn-manage-icon icon-pause" onclick="openPauseModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}', 'active')" title="Pause Listing">
                                <i class="bi bi-pause-circle text-warning"></i>
                            </button>
                            <!-- Mark as Sold Button -->
                            <button type="button" class="btn-manage-icon icon-sold" onclick="openMarkSoldModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}')" title="Mark as Sold">
                                <i class="bi bi-bag-check text-success"></i>
                            </button>
                            <!-- Delete Button -->
                            <button type="button" class="btn-manage-icon icon-delete" onclick="openDeleteModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}')" title="Delete Listing">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        @elseif($item['status'] === 'draft')
                            <!-- Continue Draft -->
                            <a href="{{ url('/post-ad?draft=' . $item['id']) }}" class="btn btn-sm btn-theme-primary px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.82rem;">
                                <i class="bi bi-pencil-square"></i> Continue
                            </a>
                            <!-- Discard Draft -->
                            <button type="button" class="btn-manage-icon icon-delete" onclick="openDeleteModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}')" title="Discard Draft">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        @elseif($item['status'] === 'sold')
                            <!-- Relist Button -->
                            <button type="button" class="btn btn-sm btn-theme-outline-primary px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.82rem;" onclick="renewListing({{ $item['id'] }}, '{{ addslashes($item['title']) }}')">
                                <i class="bi bi-arrow-repeat"></i> Relist
                            </button>
                            <!-- Delete Button -->
                            <button type="button" class="btn-manage-icon icon-delete" onclick="openDeleteModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}')" title="Delete Record">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        @elseif($item['status'] === 'expired')
                            <!-- Renew Button -->
                            <button type="button" class="btn btn-sm btn-theme-primary px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.82rem;" onclick="renewListing({{ $item['id'] }}, '{{ addslashes($item['title']) }}')">
                                <i class="bi bi-arrow-clockwise"></i> Renew
                            </button>
                            <!-- Delete Button -->
                            <button type="button" class="btn-manage-icon icon-delete" onclick="openDeleteModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}')" title="Delete Listing">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        @else
                            <!-- Resume Button -->
                            <button type="button" class="btn btn-sm btn-success text-dark fw-semibold px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.82rem;" onclick="openPauseModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}', 'paused')" title="Resume Listing">
                                <i class="bi bi-play-circle"></i> Resume
                            </button>
                            <!-- Delete Button -->
                            <button type="button" class="btn-manage-icon icon-delete" onclick="openDeleteModal({{ $item['id'] }}, '{{ addslashes($item['title']) }}')" title="Delete Listing">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        @endif

                    </div>

                </div>

            </div>
        </div>
    </div>
@endforeach
