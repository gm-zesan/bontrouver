@extends('admin.layouts.app')

@section('title', 'Live Support & Messages')

@section('content')
    <div class="container-fluid p-0 m-0 bt-admin-chat-layout" style="height: calc(100vh - 110px); overflow: hidden;">
        <div class="card border-0 rounded-0 shadow-none bg-white h-100 m-0">
            <div class="row g-0 h-100 flex-nowrap" style="overflow-x: hidden;">

                <!-- ================= LEFT SIDEBAR: USER / CONVERSATIONS LIST ================= -->
                <div class="col-12 col-md-5 col-lg-4 col-xl-3 border-end d-flex flex-column h-100 bg-white bt-sidebar-col"
                    style="border-color: #e2e8f0 !important; max-width: 360px; min-width: 280px; overflow-x: hidden;">
                    <!-- Left Sidebar Header -->
                    <div class="p-3 border-bottom bg-light flex-shrink-0" style="overflow: hidden;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-1 text-nowrap">
                                    <i class="ri-chat-3-line text-primary"></i> Messages
                                </h5>
                                @if(($stats['unread_messages'] ?? 0) > 0)
                                    <span class="badge bg-danger rounded-pill px-2 py-1 text-nowrap" style="font-size: 11px;">
                                        {{ $stats['unread_messages'] }} unread
                                    </span>
                                @endif
                            </div>
                            <span class="text-muted small text-nowrap" style="font-size: 12px;">{{ $conversations->total() }} users</span>
                        </div>

                        <!-- Modern Unified Search Input -->
                        <form action="{{ route('admin.support.index') }}" method="GET" id="supportSearchForm" class="mb-2">
                            <input type="hidden" name="filter" value="{{ $filters['filter'] ?? 'all' }}">
                            <div class="position-relative w-100 bt-search-box">
                                <i class="ri-search-2-line bt-search-icon"></i>
                                <input type="text" name="search" id="supportSearchInput" class="form-control bt-search-input"
                                    placeholder="Search user, email, message..." value="{{ $filters['search'] ?? '' }}" autocomplete="off">
                                @if(!empty($filters['search']))
                                    <a href="{{ route('admin.support.index', ['filter' => $filters['filter'] ?? 'all']) }}"
                                        class="bt-search-clear" title="Clear Search">
                                        <i class="ri-close-circle-fill"></i>
                                    </a>
                                @endif
                            </div>
                        </form>

                        <!-- Filter Tabs: All vs Unread -->
                        <div class="d-flex align-items-center gap-1 w-100">
                            @php
                                $activeFilter = $filters['filter'] ?? 'all';
                            @endphp
                            <a href="{{ route('admin.support.index', array_merge(request()->query(), ['filter' => 'all'])) }}"
                                class="btn btn-sm rounded-pill px-3 py-1 fw-semibold text-nowrap {{ $activeFilter === 'all' ? 'btn-primary text-white shadow-sm' : 'btn-light text-secondary border' }}"
                                style="font-size: 12px;">
                                All (<span id="supportAllTabCount">{{ $stats['total'] ?? 0 }}</span>)
                            </a>
                            <a href="{{ route('admin.support.index', array_merge(request()->query(), ['filter' => 'unread'])) }}"
                                class="btn btn-sm rounded-pill px-3 py-1 fw-semibold d-flex align-items-center gap-1 text-nowrap {{ $activeFilter === 'unread' ? 'btn-primary text-white shadow-sm' : 'btn-light text-secondary border' }}"
                                style="font-size: 12px;">
                                <span>Unread</span>
                                <span id="supportUnreadTabBadge" class="badge {{ $activeFilter === 'unread' ? 'bg-white text-primary' : 'bg-danger text-white' }} rounded-pill px-1 {{ ($stats['unread_conversations'] ?? 0) > 0 ? '' : 'd-none' }}" style="font-size: 10px;">
                                    {{ $stats['unread_conversations'] ?? 0 }}
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Scrollable Conversations List (Vertical Only, Never Horizontal) -->
                    <div class="flex-grow-1 bt-sidebar-list bt-scrollable-y" id="conversationsListContainer" style="overflow-y: auto !important; overflow-x: hidden !important; width: 100%;">
                        @forelse($conversations as $conv)
                            @php
                                $isSelected = $selectedConversation && $selectedConversation->id === $conv->id;
                                $unreadCount = $conv->unread_messages_for_admin_count ?? 0;
                                $user = $conv->user;
                                $latestMsg = $conv->latestMessage;
                            @endphp
                            <a id="conv-item-{{ $conv->id }}"
                                href="{{ route('admin.support.index', array_merge(request()->query(), ['conversation_id' => $conv->id])) }}"
                                class="d-flex align-items-start gap-2 px-3 py-2 border-bottom text-decoration-none bt-user-item w-100 {{ $isSelected ? 'bg-primary-subtle active-border' : ($unreadCount > 0 ? 'bg-unread-message' : 'hover-bg-light') }}"
                                data-user-name="{{ strtolower($user->name) }}"
                                data-user-email="{{ strtolower($user->email) }}"
                                data-message="{{ $latestMsg ? strtolower($latestMsg->message) : '' }}"
                                style="overflow: hidden; max-width: 100%;">

                                <!-- Avatar with Online Dot -->
                                <div class="position-relative flex-shrink-0" style="width: 42px; height: 42px;">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle border"
                                        style="width: 42px; height: 42px; object-fit: cover;"
                                        onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';">
                                    <span class="position-absolute bottom-0 end-0 p-1 {{ $unreadCount > 0 ? 'bg-danger' : 'bg-success' }} border border-white rounded-circle"></span>
                                </div>

                                <!-- Conversation Meta -->
                                <div class="flex-grow-1 min-w-0" style="width: 0; min-width: 0; overflow: hidden;">
                                    <div class="d-flex align-items-center justify-content-between mb-1" style="min-width: 0;">
                                        <div class="{{ $unreadCount > 0 ? 'fw-bold text-dark' : 'fw-semibold text-dark' }} text-truncate d-flex align-items-center gap-1"
                                            style="font-size: 13px; min-width: 0; max-width: 130px;">
                                            <span class="text-truncate">{{ $user->name }}</span>
                                            @if($user->is_verified)
                                                <i class="ri-verified-badge-fill text-primary flex-shrink-0" style="font-size: 13px;" title="Verified Canadian User"></i>
                                            @endif
                                        </div>
                                        <span class="{{ $unreadCount > 0 ? 'text-primary fw-bold' : 'text-muted' }} small flex-shrink-0 text-nowrap ms-1 conv-time-span" style="font-size: 11px;">
                                            {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}
                                        </span>
                                    </div>

                                    @if($latestMsg)
                                        <div class="text-secondary text-truncate small mb-1 conv-msg-span" style="font-size: 11.5px; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $latestMsg->sender_type === 'admin' ? 'You: ' : '' }}{{ $latestMsg->message }}
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center justify-content-between conv-unread-wrapper" style="min-width: 0;">
                                        @if($unreadCount > 0)
                                            <span class="badge bg-danger rounded-pill px-2 py-1 flex-shrink-0 ms-1 d-flex align-items-center gap-1" style="font-size: 10px;">
                                                <i class="ri-mail-unread-line"></i> {{ $unreadCount }} new
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5 text-muted p-3" id="noConversationsMsg">
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-3 mb-2"
                                    style="width: 50px; height: 50px;">
                                    <i class="ri-inbox-2-line fs-4 text-muted"></i>
                                </div>
                                <h6 class="fw-bold text-secondary mb-1" style="font-size: 13px;">No Messages Found</h6>
                                <p class="small text-muted mb-0" style="font-size: 12px;">
                                    {{ ($filters['filter'] ?? '') === 'unread' ? 'All conversations have been read.' : 'There are no conversations in your inbox.' }}
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if($conversations->hasPages())
                        <div class="p-2 border-top bg-light d-flex justify-content-center flex-shrink-0" style="overflow: hidden;">
                            {{ $conversations->withQueryString()->links() }}
                        </div>
                    @endif
                </div>


                <!-- ================= MIDDLE CONTENT: MESSAGES STREAM ================= -->
                <div class="col d-flex flex-column h-100 bg-white" style="min-width: 0; overflow-x: hidden;">
                    @if($selectedConversation)
                        @php
                            $activeUser = $selectedConversation->user;
                        @endphp
                        <!-- Middle Top Bar: User Info Header -->
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-white flex-shrink-0"
                            style="border-color: #e2e8f0 !important; overflow: hidden;">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <img src="{{ $activeUser->avatar_url }}" alt="{{ $activeUser->name }}"
                                    class="rounded-circle border flex-shrink-0" style="width: 42px; height: 42px; object-fit: cover;"
                                    onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';">
                                <div class="min-w-0">
                                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-1 text-truncate" style="font-size: 14px;">
                                        <span>{{ $activeUser->name }}</span>
                                        @if($activeUser->is_verified)
                                            <i class="ri-verified-badge-fill text-primary flex-shrink-0" style="font-size: 14px;"
                                                title="Verified Canadian User"></i>
                                        @endif
                                    </h6>
                                    <div class="text-muted small d-flex align-items-center gap-1 text-truncate" style="font-size: 12px;">
                                        <span class="text-truncate">{{ $activeUser->email }}</span>
                                        <span>•</span>
                                        <span class="text-nowrap"><i class="ri-map-pin-line text-muted"></i> {{ $activeUser->city ?? 'Canada' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right actions -->
                            <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                                <a href="{{ route('admin.users.show', $activeUser->id) }}"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-none d-sm-inline-flex align-items-center gap-1 text-nowrap"
                                    target="_blank" style="font-size: 12px;">
                                    <i class="ri-user-line"></i> View User Profile
                                </a>

                                <button type="button"
                                    class="btn btn-sm btn-light border rounded-circle d-xl-none p-2 text-muted"
                                    data-bs-toggle="offcanvas" data-bs-target="#userDossierDrawer" title="View customer info">
                                    <i class="ri-user-search-line"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Scrollable Messages Stream (Y Axis Only) -->
                        <div id="adminChatContainer"
                            class="p-3 flex-grow-1 bt-scrollable-y d-flex flex-column gap-3"
                            style="background-color: #f8fafc; overflow-y: auto !important; overflow-x: hidden !important;">
                            @foreach($selectedConversation->messages as $msg)
                                @php
                                    $isAdmin = $msg->sender_type === 'admin';
                                    $isSystem = $msg->sender_type === 'system';
                                    $isWelcomeText = str_contains($msg->message, 'Welcome to Bon Trouver Support');
                                    $avatarSrc = $isAdmin 
                                        ? ($msg->sender?->avatar_url ?: asset('images/support-avatar.svg')) 
                                        : ($activeUser->avatar_url ?: asset('images/default-avatar.svg'));
                                @endphp

                                @if($isWelcomeText)
                                    @continue
                                @endif

                                @if($isSystem)
                                    <div class="align-self-center bg-white border border-dashed rounded-3 px-3 py-2 text-center text-muted small shadow-sm"
                                        style="font-size: 12px; max-width: 85%; word-break: break-word;">
                                        <i class="ri-information-line text-primary me-1"></i> {{ $msg->message }}
                                    </div>
                                @else
                                    <div class="d-flex gap-2 {{ $isAdmin ? 'align-self-end flex-row-reverse' : 'align-self-start' }}"
                                        style="max-width: 80%; min-width: 0;">
                                        <img src="{{ $avatarSrc }}" alt="avatar"
                                            class="rounded-circle flex-shrink-0"
                                            style="width: 32px; height: 32px; object-fit: cover; border: 1px solid rgba(0,0,0,0.08);"
                                            onerror="this.onerror=null;this.src='{{ $isAdmin ? asset('images/support-avatar.svg') : asset('images/default-avatar.svg') }}';">
                                        <div style="min-width: 0;">
                                            <div class="px-3 py-2 rounded-4 shadow-sm {{ $isAdmin ? 'bg-primary text-white' : 'bg-white text-dark border' }}"
                                                style="font-size: 13px; line-height: 1.5; border-radius: {{ $isAdmin ? '18px 4px 18px 18px' : '4px 18px 18px 18px' }} !important; word-break: break-word; overflow-wrap: break-word;">
                                                <div class="fw-bold mb-1"
                                                    style="font-size: 11px; opacity: {{ $isAdmin ? '0.9' : '0.7' }};">
                                                    {{ $isAdmin ? ($msg->sender ? $msg->sender->name : 'Bon Trouver Support') : $activeUser->name }}
                                                </div>
                                                {{ $msg->message }}

                                                @php
                                                    $files = $msg->attachment_files;
                                                @endphp
                                                @if(!empty($files))
                                                    <div class="mt-2 d-flex flex-column gap-2">
                                                        @foreach($files as $f)
                                                            @if($f['is_image'])
                                                                <div class="position-relative d-inline-block rounded-3 overflow-hidden shadow-sm" style="border: 1px solid rgba(0,0,0,0.1); background: #081D33;">
                                                                    <a href="javascript:void(0)" onclick="openAdminLightbox('{{ $f['url'] }}', '{{ addslashes($f['name']) }}')">
                                                                        <img src="{{ $f['url'] }}" alt="{{ $f['name'] }}" class="img-fluid rounded-3" style="max-height: 160px; max-width: 220px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                                                    </a>
                                                                    <a href="{{ $f['url'] }}" download="{{ $f['name'] }}" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-1 rounded-circle border border-secondary border-opacity-25 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);" title="Download">
                                                                        <i class="ri-download-2-line text-white" style="font-size: 0.75rem;"></i>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="p-2 {{ $isAdmin ? 'bg-white bg-opacity-20 text-white' : 'bg-light text-dark' }} rounded-3 d-flex align-items-center gap-2 border {{ $isAdmin ? 'border-white-20' : 'border-secondary border-opacity-10' }}" style="max-width: 260px;">
                                                                    <i class="ri-file-text-fill fs-4 {{ $isAdmin ? 'text-white' : 'text-success' }}"></i>
                                                                    <div class="min-w-0 me-2">
                                                                        <div class="small fw-semibold text-truncate" style="font-size: 0.8rem; max-width: 150px;" title="{{ $f['name'] }}">{{ $f['name'] }}</div>
                                                                    </div>
                                                                    <a href="{{ $f['url'] }}" download="{{ $f['name'] }}" class="btn btn-sm {{ $isAdmin ? 'btn-outline-light' : 'btn-outline-secondary' }} py-0 px-2" style="font-size: 0.75rem;">Download</a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-muted small mt-1 {{ $isAdmin ? 'text-end' : '' }}"
                                                style="font-size: 11px;">
                                                {{ $msg->created_at ? $msg->created_at->format('g:i A') : '' }}
                                                @if($isAdmin)
                                                    <i class="ri-check-double-line text-primary ms-1" title="Delivered"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Composer / Reply Bar -->
                        <div class="p-3 border-top bg-white flex-shrink-0" style="border-color: #e2e8f0 !important;">
                            <!-- Quick Canned Response Pills -->
                            <div class="d-flex align-items-center gap-1 mb-2 overflow-x-auto pb-1" style="scrollbar-width: none;">
                                <span class="text-muted small text-nowrap" style="font-size: 11px; font-weight: 600;">
                                    <i class="ri-flashlight-fill text-warning"></i> Quick:
                                </span>
                                <button type="button"
                                    class="btn btn-sm btn-light border rounded-pill py-1 px-2 small text-nowrap hover-bg-primary-subtle"
                                    onclick="insertCannedResponse('verification')" style="font-size: 11px;">ID Verified</button>
                                <button type="button"
                                    class="btn btn-sm btn-light border rounded-pill py-1 px-2 small text-nowrap hover-bg-primary-subtle"
                                    onclick="insertCannedResponse('boost')" style="font-size: 11px;">Boost Active</button>
                                <button type="button"
                                    class="btn btn-sm btn-light border rounded-pill py-1 px-2 small text-nowrap hover-bg-primary-subtle"
                                    onclick="insertCannedResponse('safety')" style="font-size: 11px;">Safety Tips</button>
                                <button type="button"
                                    class="btn btn-sm btn-light border rounded-pill py-1 px-2 small text-nowrap hover-bg-primary-subtle"
                                    onclick="insertCannedResponse('help')" style="font-size: 11px;">Need Help?</button>
                            </div>

                            <!-- Attachment Preview -->
                            <div id="adminAttachmentPreview"
                                class="d-none mb-2 bg-light rounded-3 px-2 py-2 text-secondary"
                                style="font-size: 12px;">
                                <div id="adminAttachmentPreviewList" class="d-flex flex-wrap gap-2 w-100"></div>
                            </div>

                            <!-- Reply Input Form -->
                            <form id="adminReplyForm" class="d-flex align-items-end gap-2">
                                <input type="file" id="adminAttachmentInput" class="d-none"
                                    accept="image/*,.pdf,.doc,.docx,.txt,.rtf,.xls,.xlsx,.csv" multiple>

                                <button type="button"
                                    class="btn btn-light border rounded-circle p-2 text-muted hover-text-dark d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 38px; height: 38px;"
                                    onclick="document.getElementById('adminAttachmentInput').click()"
                                    title="Attach image or document (PDF, Doc, Excel, etc.)">
                                    <i class="ri-attachment-2 fs-5"></i>
                                </button>

                                <div class="flex-grow-1 min-w-0">
                                    <textarea id="adminMessageInput" rows="1" class="form-control rounded-4 py-2 px-3 border"
                                        placeholder="Type your reply to {{ $activeUser->name }} (Press Enter to send)..."
                                        style="resize: none; font-size: 13px; max-height: 100px;"></textarea>
                                </div>

                                <button type="submit" id="adminSendReplyBtn"
                                    class="btn btn-primary rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 38px; height: 38px;" title="Send Reply">
                                    <i class="ri-send-plane-2-fill text-white fs-5" style="margin-left: 2px;"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- No conversation selected -->
                        <div
                            class="d-flex flex-column align-items-center justify-content-center h-100 p-5 text-center bg-light">
                            <div class="rounded-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center p-4 mb-3 border"
                                style="width: 80px; height: 80px;">
                                <i class="ri-chat-voice-line display-6 text-primary"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Select a Conversation</h5>
                            <p class="text-muted small mb-0" style="max-width: 320px; font-size: 13px;">
                                Choose an inquiry from the left sidebar to review history, view user details, and reply in real time.
                            </p>
                        </div>
                    @endif
                </div>


                <!-- ================= RIGHT SIDEBAR: USER INFO ================= -->
                <div class="col-xl-3 d-none d-xl-flex flex-column h-100 bg-white border-start bt-scrollable-y flex-shrink-0"
                    style="border-color: #e2e8f0 !important; width: 300px; overflow-y: auto !important; overflow-x: hidden !important;">
                    @if($selectedConversation)
                        @php
                            $u = $selectedConversation->user;
                        @endphp
                        <div class="p-3" style="overflow-x: hidden;">
                            <!-- User Card -->
                            <div class="card border-0 p-3 text-center mb-3">
                                <div class="position-relative d-inline-block mx-auto mb-2">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="rounded-circle border p-1"
                                        style="width: 68px; height: 68px; object-fit: cover;"
                                        onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';">
                                    @if($u->is_verified)
                                        <span
                                            class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 d-flex align-items-center justify-content-center"
                                            style="width: 20px; height: 20px; font-size: 11px;" title="Verified Canadian User">
                                            <i class="ri-check-line"></i>
                                        </span>
                                    @endif
                                </div>

                                <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate">{{ $u->name }}</h6>
                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1 mb-2 mx-auto text-truncate"
                                    style="font-size: 11px; max-width: 160px;">
                                    {{ $u->member_tier['name'] ?? 'New Member' }}
                                </span>

                                <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                                    <a href="{{ route('admin.users.show', $u->id) }}"
                                        class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold text-nowrap"
                                        target="_blank" style="font-size: 12px;">
                                        <i class="ri-user-line me-1"></i> Full Profile
                                    </a>
                                </div>
                            </div>

                            <!-- Contact & Location -->
                            <div class="card border-0 p-3 mb-3">
                                <h6 class="text-uppercase text-muted fw-bold mb-2"
                                    style="font-size: 11px; letter-spacing: 0.05em;">Contact & Location</h6>

                                <div class="d-flex flex-column gap-2 small">
                                    <div class="d-flex align-items-center justify-content-between" style="min-width: 0;">
                                        <span class="text-muted text-nowrap me-1"><i class="ri-mail-line me-1 text-secondary"></i> Email:</span>
                                        <span class="fw-medium text-dark text-truncate"
                                            style="max-width: 160px;" title="{{ $u->email }}">{{ $u->email }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted text-nowrap"><i class="ri-phone-line me-1 text-secondary"></i> Phone:</span>
                                        <span class="fw-medium text-dark text-truncate">{{ $u->phone ?? 'Not provided' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted text-nowrap"><i class="ri-map-pin-line me-1 text-secondary"></i> Location:</span>
                                        <span class="fw-medium text-dark text-truncate">{{ $u->city ?? 'Canada' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted text-nowrap"><i class="ri-shield-check-line me-1 text-secondary"></i> Status:</span>
                                        <span
                                            class="badge {{ $u->is_verified ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-1"
                                            style="font-size: 11px;">
                                            {{ $u->is_verified ? 'Verified' : 'Unverified' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Marketplace Activity -->
                            <div class="card border-0 p-3">
                                <h6 class="text-uppercase text-muted fw-bold mb-2"
                                    style="font-size: 11px; letter-spacing: 0.05em;">Marketplace Activity</h6>

                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <div class="p-2 rounded-3 bg-light">
                                            <div class="fs-5 fw-bold text-primary">{{ $u->listings()->count() }}</div>
                                            <small class="text-muted" style="font-size: 11px;">Listings</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 rounded-3 bg-light">
                                            <div class="fs-5 fw-bold text-success">{{ $u->community_points ?? 0 }}</div>
                                            <small class="text-muted" style="font-size: 11px;">Points</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-muted small text-center mt-2" style="font-size: 11px;">
                                    Member since {{ $u->created_at ? $u->created_at->format('M Y') : 'Recent' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted my-auto">
                            <i class="ri-information-line fs-3 mb-1"></i>
                            <p class="small mb-0">Select a user to view contact and profile information.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Admin Full Screen Image Modal -->
    <div class="modal fade" id="adminImagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0 position-relative">
                    <img id="adminModalImagePreview" src="" class="img-fluid rounded shadow-lg" style="max-height: 85vh; object-fit: contain;">
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <a id="adminModalDownloadBtn" href="" download class="btn btn-primary px-4 rounded-pill shadow">
                        <i class="ri-download-2-line me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bt-admin-attached-img-wrap:hover .bt-admin-img-overlay {
            opacity: 1;
        }
        .bt-admin-img-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.45);
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity 0.2s ease;
        }
        .bt-admin-attach-preview-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 4px 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        /* ================= Search Input & Focus Design ================= */
        .bt-search-box {
            position: relative;
            width: 100%;
        }

        .bt-search-icon {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            font-size: 15px;
            color: #94a3b8;
            pointer-events: none;
            z-index: 2;
            transition: color 0.2s ease;
        }

        .bt-search-input {
            height: 36px;
            padding-left: 36px !important;
            padding-right: 32px !important;
            border-radius: 8px !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 12.5px !important;
            color: #0f172a !important;
            box-shadow: none !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .bt-search-input::placeholder {
            color: #94a3b8 !important;
            font-weight: 400;
        }

        .bt-search-input:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }

        .bt-search-box:focus-within .bt-search-icon {
            color: #49D17D !important;
        }

        .bt-search-input:focus {
            background-color: #ffffff !important;
            border-color: #49D17D !important;
            box-shadow: 0 0 0 3px rgba(73, 209, 125, 0.18) !important;
            outline: none !important;
        }

        .bt-search-clear {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            z-index: 2;
            transition: color 0.15s ease;
        }

        .bt-search-clear:hover {
            color: #ef4444;
        }

        .bt-sidebar-col {
            overflow-x: hidden !important;
        }

        .bt-sidebar-list {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        .bt-user-item {
            transition: background-color 0.15s ease;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow-x: hidden !important;
        }

        .bt-user-item.active-border {
            border-left: 3px solid var(--color-primary, #49D17D) !important;
        }

        .bg-unread-message {
            background-color: #f0fdf4 !important;
            border-left: 3px solid #ef4444 !important;
        }

        .bt-scrollable-y::-webkit-scrollbar {
            width: 5px;
        }

        .bt-scrollable-y::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.18);
            border-radius: 4px;
        }

        .bt-scrollable-y::-webkit-scrollbar-track {
            background: transparent;
        }

        .hover-bg-primary-subtle:hover {
            background-color: rgba(73, 209, 125, 0.15) !important;
            color: #065F46 !important;
        }

        /* Hide outer page scrollbar when on support route */
        body {
            overflow: hidden !important;
        }
    </style>

    <script>
        // Global Admin Lightbox Functions
        window.openAdminLightbox = function (url, title = 'Image Preview') {
            const img = document.getElementById('adminModalImagePreview');
            const dlBtn = document.getElementById('adminModalDownloadBtn');
            if (img) img.src = url;
            if (dlBtn) {
                dlBtn.href = url;
                dlBtn.setAttribute('download', title || 'image');
            }
            const modalEl = document.getElementById('adminImagePreviewModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        };

        window.closeAdminLightbox = function () {
            const modalEl = document.getElementById('adminImagePreviewModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            const currentFilter = "{{ $filters['filter'] ?? 'all' }}";
            let currentSearch = "{{ $filters['search'] ?? '' }}";
            let currentConversationId = {{ $selectedConversation ? $selectedConversation->id : 'null' }};
            let lastMsgId = {{ $selectedConversation ? ($selectedConversation->messages->max('id') ?? 0) : 0 }};

            // Search filter input
            const searchInput = document.getElementById('supportSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    currentSearch = query;
                    const items = document.querySelectorAll('.bt-user-item');

                    items.forEach(item => {
                        const name = item.getAttribute('data-user-name') || '';
                        const email = item.getAttribute('data-user-email') || '';
                        const message = item.getAttribute('data-message') || '';

                        if (!query || name.includes(query) || email.includes(query) || message.includes(query)) {
                            item.style.removeProperty('display');
                        } else {
                            item.style.setProperty('display', 'none', 'important');
                        }
                    });
                });
            }

            // Attachment Handling in Admin Composer
            let selectedAdminFiles = [];
            const fileInput = document.getElementById('adminAttachmentInput');
            const attachPreview = document.getElementById('adminAttachmentPreview');
            const attachPreviewList = document.getElementById('adminAttachmentPreviewList');

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    if (this.files && this.files.length > 0) {
                        Array.from(this.files).forEach(f => {
                            if (!selectedAdminFiles.some(item => item.name === f.name && item.size === f.size)) {
                                if (selectedAdminFiles.length < 5) {
                                    selectedAdminFiles.push(f);
                                }
                            }
                        });
                        renderAdminAttachmentPreviews();
                    }
                    this.value = '';
                });
            }

            function renderAdminAttachmentPreviews() {
                if (!attachPreview || !attachPreviewList) return;
                if (selectedAdminFiles.length === 0) {
                    attachPreview.classList.add('d-none');
                    attachPreviewList.innerHTML = '';
                    return;
                }

                attachPreview.classList.remove('d-none');
                attachPreviewList.innerHTML = '';

                selectedAdminFiles.forEach((file, index) => {
                    const itemEl = document.createElement('div');
                    itemEl.className = 'position-relative d-inline-block bg-white rounded p-1 border shadow-sm';
                    itemEl.style.minWidth = '50px';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center shadow';
                    removeBtn.style.width = '18px';
                    removeBtn.style.height = '18px';
                    removeBtn.style.transform = 'translate(30%, -30%)';
                    removeBtn.innerHTML = '<i class="ri-close-line" style="font-size: 11px;"></i>';
                    removeBtn.onclick = () => removeAdminSelectedFile(index);

                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.className = 'rounded object-fit-cover';
                        img.style.width = '50px';
                        img.style.height = '50px';
                        const reader = new FileReader();
                        reader.onload = e => img.src = e.target.result;
                        reader.readAsDataURL(file);
                        itemEl.appendChild(img);
                    } else {
                        itemEl.innerHTML = `<div class="d-flex flex-column align-items-center justify-content-center text-dark" style="width: 50px; height: 50px;"><i class="ri-file-text-fill fs-4 text-success"></i></div>`;
                    }

                    itemEl.appendChild(removeBtn);
                    attachPreviewList.appendChild(itemEl);
                });
            }

            window.removeAdminSelectedFile = function(index) {
                selectedAdminFiles.splice(index, 1);
                renderAdminAttachmentPreviews();
            };

            window.removeAdminAttachment = function () {
                selectedAdminFiles = [];
                renderAdminAttachmentPreviews();
            };

            // Open Active Conversation Setup
            @if($selectedConversation)
                const chatContainer = document.getElementById('adminChatContainer');
                const replyForm = document.getElementById('adminReplyForm');
                const messageInput = document.getElementById('adminMessageInput');
                const sendBtn = document.getElementById('adminSendReplyBtn');

                function scrollChatToBottom() {
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                }
                scrollChatToBottom();

                if (messageInput) {
                    messageInput.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            replyForm.dispatchEvent(new Event('submit'));
                        }
                    });
                }

                window.insertCannedResponse = function (type) {
                    let text = '';
                    if (type === 'verification') {
                        text = 'Hi {{ $selectedConversation->user->name }}, great news! Your Canadian identity verification has been reviewed and approved. Your verified badge is now active on your profile.';
                    } else if (type === 'boost') {
                        text = 'Hi {{ $selectedConversation->user->name }}, your listing promotion boost has been activated successfully and is now featured in search results.';
                    } else if (type === 'safety') {
                        text = 'Hi {{ $selectedConversation->user->name }}, thank you for reaching out. Please remember to keep all transactions in safe public meetup locations and never send money in advance via wire transfer.';
                    } else if (type === 'help') {
                        text = 'Hi {{ $selectedConversation->user->name }}, thank you for reaching out! How can we assist you today?';
                    }
                    if (messageInput) {
                        messageInput.value = text;
                        messageInput.focus();
                    }
                };

                if (replyForm) {
                    replyForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const text = messageInput.value.trim();
                        const hasFiles = selectedAdminFiles.length > 0;

                        if (!text && !hasFiles) return;

                        sendBtn.disabled = true;
                        sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                        const formData = new FormData();
                        formData.append('message', text);
                        formData.append('_token', '{{ csrf_token() }}');
                        
                        selectedAdminFiles.forEach(file => {
                            formData.append('attachments[]', file);
                        });

                        fetch(`/admin/support/${currentConversationId}/reply`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = '<i class="ri-send-plane-2-fill text-white fs-5" style="margin-left: 2px;"></i>';

                            if (data.status === 'success' && data.message) {
                                messageInput.value = '';
                                selectedAdminFiles = [];
                                renderAdminAttachmentPreviews();

                                if (data.message.id > lastMsgId) {
                                    lastMsgId = data.message.id;
                                }

                                appendAdminMessage(data.message);
                                scrollChatToBottom();
                            }
                        })
                        .catch(err => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = '<i class="ri-send-plane-2-fill text-white fs-5" style="margin-left: 2px;"></i>';
                            console.error('Reply failed:', err);
                        });
                    });
                }
            @endif

            function resolveSafeAdminUrl(url) {
                if (!url) return '';
                return url.replace(/^https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?(\/.*)$/i, '$1');
            }

            // Helper to generate attachments HTML in admin chat
            function buildAdminAttachmentsHtml(msg, isAdmin) {
                const files = msg.attachment_files || (msg.attachments ? msg.attachments.map(u => {
                    const uStr = typeof u === 'string' ? u : (u.url || u.path || '');
                    const uName = (typeof u === 'object' && u.name) ? u.name : (uStr.split('/').pop() || 'Attachment');
                    return {
                        url: uStr,
                        name: uName,
                        is_image: /\.(jpg|jpeg|png|webp|gif|svg)$/i.test(uName || uStr),
                        is_pdf: /\.pdf$/i.test(uName || uStr),
                        is_doc: /\.(doc|docx|txt|rtf)$/i.test(uName || uStr),
                        is_spreadsheet: /\.(xls|xlsx|csv)$/i.test(uName || uStr),
                    };
                }) : []);

                if (!files || files.length === 0) return '';

                let html = '<div class="mt-2 d-flex flex-column gap-2">';
                files.forEach(f => {
                    const rawUrl = f.url || (typeof f === 'string' ? f : '');
                    const safeUrl = escapeHtml(resolveSafeAdminUrl(rawUrl));
                    const safeName = escapeHtml(f.name || rawUrl.split('/').pop() || 'Attachment');
                    const sizeStr = f.size_human ? `(${f.size_human})` : '';

                    if (f.is_image) {
                        html += `
                            <div class="position-relative d-inline-block rounded-3 overflow-hidden shadow-sm" style="border: 1px solid rgba(0,0,0,0.1); background: #081D33;">
                                <a href="javascript:void(0)" onclick="openAdminLightbox('${safeUrl}', '${safeName}')">
                                    <img src="${safeUrl}" alt="${safeName}" class="img-fluid rounded-3" style="max-height: 160px; max-width: 220px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                </a>
                                <a href="${safeUrl}" download="${safeName}" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-1 rounded-circle border border-secondary border-opacity-25 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);" title="Download">
                                    <i class="ri-download-2-line text-white" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="p-2 ${isAdmin ? 'bg-white bg-opacity-20 text-white' : 'bg-light text-dark'} rounded-3 d-flex align-items-center gap-2 border ${isAdmin ? 'border-white-20' : 'border-secondary border-opacity-10'}" style="max-width: 260px;">
                                <i class="ri-file-text-fill fs-4 ${isAdmin ? 'text-white' : 'text-success'}"></i>
                                <div class="min-w-0 me-2">
                                    <div class="small fw-semibold text-truncate" style="font-size: 0.8rem; max-width: 150px;" title="${safeName}">${safeName}</div>
                                </div>
                                <a href="${safeUrl}" download="${safeName}" class="btn btn-sm ${isAdmin ? 'btn-outline-light' : 'btn-outline-secondary'} py-0 px-2" style="font-size: 0.75rem;">Download</a>
                            </div>
                        `;
                    }
                });
                html += '</div>';
                return html;
            }

            function appendAdminMessage(msg) {
                const chatContainer = document.getElementById('adminChatContainer');
                if (!chatContainer) return;

                const msgEl = document.createElement('div');
                msgEl.className = 'd-flex gap-2 align-self-end flex-row-reverse';
                msgEl.style.maxWidth = '80%';
                msgEl.style.minWidth = '0';

                const attachmentsHtml = buildAdminAttachmentsHtml(msg, true);
                const adminAvatar = msg.sender_avatar && msg.sender_avatar !== 'null' ? msg.sender_avatar : '{{ asset('images/support-avatar.svg') }}';

                msgEl.innerHTML = `
                    <img src="${adminAvatar}" alt="avatar" class="rounded-circle flex-shrink-0" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid rgba(0,0,0,0.08);" onerror="this.onerror=null;this.src='{{ asset('images/support-avatar.svg') }}';">
                    <div style="min-width: 0;">
                        <div class="p-3 rounded-4 shadow-sm bg-primary text-white" style="font-size: 13px; line-height: 1.5; border-radius: 18px 4px 18px 18px !important; word-break: break-word; overflow-wrap: break-word;">
                            <div class="fw-bold mb-1" style="font-size: 11px; opacity: 0.9;">${escapeHtml(msg.sender_name || 'Bon Trouver Support')}</div>
                            ${msg.message ? escapeHtml(msg.message) : ''}
                            ${attachmentsHtml}
                        </div>
                        <div class="text-muted small mt-1 text-end" style="font-size: 11px;">
                            ${msg.created_at_time || 'Just now'}
                            <i class="ri-check-double-line text-primary ms-1" title="Delivered"></i>
                        </div>
                    </div>
                `;
                chatContainer.appendChild(msgEl);
            }

            function appendUserMessage(msg) {
                const chatContainer = document.getElementById('adminChatContainer');
                if (!chatContainer) return;

                const msgEl = document.createElement('div');
                msgEl.className = 'd-flex gap-2 align-self-start';
                msgEl.style.maxWidth = '80%';
                msgEl.style.minWidth = '0';

                const attachmentsHtml = buildAdminAttachmentsHtml(msg, false);
                const userAvatar = msg.sender_avatar && msg.sender_avatar !== 'null' ? msg.sender_avatar : '{{ asset('images/default-avatar.svg') }}';

                msgEl.innerHTML = `
                    <img src="${userAvatar}" alt="avatar" class="rounded-circle flex-shrink-0" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid rgba(0,0,0,0.08);" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';">
                    <div style="min-width: 0;">
                        <div class="p-3 rounded-4 shadow-sm bg-white text-dark border" style="font-size: 13px; line-height: 1.5; border-radius: 4px 18px 18px 18px !important; word-break: break-word; overflow-wrap: break-word;">
                            <div class="fw-bold mb-1 text-secondary" style="font-size: 11px;">${escapeHtml(msg.sender_name || 'User')}</div>
                            ${msg.message ? escapeHtml(msg.message) : ''}
                            ${attachmentsHtml}
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 11px;">${msg.created_at_time || 'Just now'}</div>
                    </div>
                `;
                chatContainer.appendChild(msgEl);
            }

            // ================= REAL-TIME WEBSOCKETS (LARAVEL REVERB / ECHO) =================
            if (window.Echo) {
                // 1. Listen to global Admin Support Channel
                window.Echo.private('admin.support')
                    .listen('SupportMessageSent', (e) => {
                        const isCurrentActive = currentConversationId && (parseInt(currentConversationId) === parseInt(e.support_conversation_id));
                        const isFromSelf = e.sender_id === {{ auth()->id() ?? 0 }};

                        // A. If active conversation is currently open and message is from user
                        if (isCurrentActive && !isFromSelf) {
                            if (e.id > lastMsgId) {
                                lastMsgId = e.id;
                                appendUserMessage(e);
                                scrollChatToBottom();
                            }
                        }

                        // B. Real-Time Left Sidebar Update & Re-ordering
                        const listContainer = document.getElementById('conversationsListContainer');
                        const emptyMsg = document.getElementById('noConversationsMsg');
                        if (emptyMsg) emptyMsg.remove();

                        let item = document.getElementById(`conv-item-${e.support_conversation_id}`);
                        if (item && listContainer) {
                            // Update last message & time
                            const timeSpan = item.querySelector('.conv-time-span');
                            if (timeSpan) timeSpan.innerText = e.created_at_time || 'Just now';

                            const msgSpan = item.querySelector('.conv-msg-span');
                            if (msgSpan) {
                                msgSpan.innerHTML = `${e.is_admin ? 'You: ' : ''}${escapeHtml(e.message || 'Attachment')}`;
                            }

                            // If not current conversation and not self, increment badge
                            if (!isCurrentActive && !isFromSelf) {
                                const unreadWrapper = item.querySelector('.conv-unread-wrapper');
                                if (unreadWrapper) {
                                    unreadWrapper.innerHTML = `<span class="badge bg-danger rounded-pill px-2 py-1 flex-shrink-0 ms-1 d-flex align-items-center gap-1" style="font-size: 10px;"><i class="ri-mail-unread-line"></i> New</span>`;
                                }
                                item.classList.add('bg-unread-message');
                                item.classList.remove('hover-bg-light');

                                // Update unread stats counter
                                const unreadTabBadge = document.getElementById('supportUnreadTabBadge');
                                if (unreadTabBadge) {
                                    let currentCount = parseInt(unreadTabBadge.innerText || '0', 10) || 0;
                                    unreadTabBadge.innerText = currentCount + 1;
                                    unreadTabBadge.classList.remove('d-none');
                                }
                            }

                            // Bump this conversation to the top of the sidebar list
                            listContainer.prepend(item);
                        } else if (!item && listContainer) {
                            // Fetch fresh sidebar list
                            triggerBackgroundSync();
                        }
                    });

                @if($selectedConversation)
                // 2. Also listen specifically to the selected support conversation
                window.Echo.private('support.conversation.{{ $selectedConversation->id }}')
                    .listen('SupportMessageSent', (e) => {
                        if (e.sender_id !== {{ auth()->id() ?? 0 }}) {
                            if (e.id > lastMsgId) {
                                lastMsgId = e.id;
                                appendUserMessage(e);
                                scrollChatToBottom();
                            }
                        }
                    });
                @endif
            }

            // Fallback sync poller (runs every 15 seconds)
            function triggerBackgroundSync() {
                const pollUrl = `{{ route('admin.support.pollInbox') }}?filter=${currentFilter}&search=${encodeURIComponent(currentSearch)}&current_conversation_id=${currentConversationId || ''}&after_id=${lastMsgId}`;

                fetch(pollUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status !== 'success') return;

                    // 1. Process new messages in open conversation
                    if (data.new_messages && data.new_messages.length > 0) {
                        data.new_messages.forEach(msg => {
                            if (msg.id > lastMsgId) {
                                lastMsgId = msg.id;
                                if (!msg.is_admin) {
                                    appendUserMessage(msg);
                                }
                            }
                        });
                        scrollChatToBottom();
                    }

                    // 2. Update Stats Counter Badges
                    if (data.stats) {
                        const allTabCount = document.getElementById('supportAllTabCount');
                        if (allTabCount && data.stats.total !== undefined) {
                            allTabCount.innerText = data.stats.total;
                        }
                        const unreadTabBadge = document.getElementById('supportUnreadTabBadge');
                        if (unreadTabBadge && data.stats.unread_conversations !== undefined) {
                            unreadTabBadge.innerText = data.stats.unread_conversations;
                            if (data.stats.unread_conversations > 0) {
                                unreadTabBadge.classList.remove('d-none');
                            } else {
                                unreadTabBadge.classList.add('d-none');
                            }
                        }
                    }

                    // 3. Sync Conversation List on left sidebar
                    if (data.conversations) {
                        const listContainer = document.getElementById('conversationsListContainer');
                        const emptyMsg = document.getElementById('noConversationsMsg');

                        if (data.conversations.length > 0 && emptyMsg) {
                            emptyMsg.remove();
                        }

                        data.conversations.forEach((conv) => {
                            let item = document.getElementById(`conv-item-${conv.id}`);
                            const isCurrentSelected = currentConversationId === conv.id;

                            if (!item && listContainer) {
                                item = document.createElement('a');
                                item.id = `conv-item-${conv.id}`;
                                item.href = conv.url;
                                item.className = `d-flex align-items-start gap-2 px-3 py-2 border-bottom text-decoration-none bt-user-item w-100 ${isCurrentSelected ? 'bg-primary-subtle active-border' : (conv.unread_count > 0 ? 'bg-unread-message' : 'hover-bg-light')}`;
                                item.style.overflow = 'hidden';
                                item.style.maxWidth = '100%';
                                item.setAttribute('data-user-name', conv.user_name.toLowerCase());
                                item.setAttribute('data-user-email', conv.user_email.toLowerCase());
                                item.setAttribute('data-message', conv.latest_message.toLowerCase());

                                item.innerHTML = `
                                    <div class="position-relative flex-shrink-0" style="width: 42px; height: 42px;">
                                        <img src="${conv.user_avatar}" alt="${escapeHtml(conv.user_name)}" class="rounded-circle border" style="width: 42px; height: 42px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';">
                                        <span class="position-absolute bottom-0 end-0 p-1 ${conv.unread_count > 0 ? 'bg-danger' : 'bg-success'} border border-white rounded-circle"></span>
                                    </div>
                                    <div class="flex-grow-1 min-w-0" style="width: 0; min-width: 0; overflow: hidden;">
                                        <div class="d-flex align-items-center justify-content-between mb-1" style="min-width: 0;">
                                            <div class="${conv.unread_count > 0 ? 'fw-bold text-dark' : 'fw-semibold text-dark'} text-truncate d-flex align-items-center gap-1" style="font-size: 13px; min-width: 0; max-width: 130px;">
                                                <span class="text-truncate">${escapeHtml(conv.user_name)}</span>
                                                ${conv.is_verified ? '<i class="ri-verified-badge-fill text-primary flex-shrink-0" style="font-size: 13px;" title="Verified Canadian User"></i>' : ''}
                                            </div>
                                            <span class="${conv.unread_count > 0 ? 'text-primary fw-bold' : 'text-muted'} small flex-shrink-0 text-nowrap ms-1 conv-time-span" style="font-size: 11px;">
                                                ${conv.last_message_time_human}
                                            </span>
                                        </div>
                                        <div class="text-secondary text-truncate small mb-1 conv-msg-span" style="font-size: 11.5px; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            ${conv.latest_message_sender_type === 'admin' ? 'You: ' : ''}${escapeHtml(conv.latest_message)}
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between conv-unread-wrapper" style="min-width: 0;">
                                            ${conv.unread_count > 0 ? `<span class="badge bg-danger rounded-pill px-2 py-1 flex-shrink-0 ms-1 d-flex align-items-center gap-1" style="font-size: 10px;"><i class="ri-mail-unread-line"></i> ${conv.unread_count} new</span>` : ''}
                                        </div>
                                    </div>
                                `;

                                listContainer.prepend(item);
                            } else if (item) {
                                item.setAttribute('data-message', conv.latest_message.toLowerCase());
                                const timeSpan = item.querySelector('.conv-time-span');
                                if (timeSpan) timeSpan.innerText = conv.last_message_time_human;

                                const msgSpan = item.querySelector('.conv-msg-span');
                                if (msgSpan) {
                                    msgSpan.innerHTML = `${conv.latest_message_sender_type === 'admin' ? 'You: ' : ''}${escapeHtml(conv.latest_message)}`;
                                }

                                const unreadWrapper = item.querySelector('.conv-unread-wrapper');
                                if (unreadWrapper) {
                                    if (conv.unread_count > 0 && !isCurrentSelected) {
                                        unreadWrapper.innerHTML = `<span class="badge bg-danger rounded-pill px-2 py-1 flex-shrink-0 ms-1 d-flex align-items-center gap-1" style="font-size: 10px;"><i class="ri-mail-unread-line"></i> ${conv.unread_count} new</span>`;
                                        if (!isCurrentSelected) {
                                            item.classList.add('bg-unread-message');
                                            item.classList.remove('hover-bg-light');
                                        }
                                    } else {
                                        unreadWrapper.innerHTML = '';
                                        item.classList.remove('bg-unread-message');
                                        if (!isCurrentSelected) item.classList.add('hover-bg-light');
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(e => { });
            }

            setInterval(triggerBackgroundSync, 15000);

            function escapeHtml(string) {
                if (!string) return '';
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return string.replace(/[&<>"']/g, function (m) { return map[m]; });
            }
        });
    </script>
@endsection