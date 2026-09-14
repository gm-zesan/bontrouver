@extends('frontend.layouts.app', [
    'title' => 'Messages & Inbox | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Chat with buyers and sellers in real time, negotiate deals, and manage marketplace conversations.'
])

@section('content')
<div class="account-dashboard-wrapper py-4 py-lg-5">
    <div class="container-xl">
        
        <!-- Mobile Top Nav -->
        <div class="d-lg-none mb-4">
            <div class="mobile-account-nav-wrap">
                <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-person-fill me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('listings.my') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-collection-play-fill me-1"></i> My Listings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/favorites') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-heart-fill me-1"></i> Favorites
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/messages') }}" class="nav-link mobile-dark-pill active">
                            <i class="bi bi-chat-left-text-fill me-1"></i> Messages
                            <span class="badge bg-danger ms-1">2</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/notifications') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-bell-fill me-1"></i> Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/settings') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-gear-fill me-1"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row g-4 g-xl-5">
            
            <!-- Left Sidebar Navigation (Desktop >= 992px) -->
            <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 85px; z-index: 10;">
                    @include('frontend.partials.account-sidebar', ['activeNav' => 'messages', 'stats' => $stats])
                </div>
            </div>

            <!-- Main Content Area: Modern 2-Panel Messaging Interface -->
            <div class="col-12 col-lg-8 col-xl-9">

                <!-- Messaging Card Container -->
                <div class="dark-surface-card overflow-hidden" 
                     style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; min-height: 600px; height: calc(100vh - 260px); max-height: 750px;">
                    
                    <div class="row g-0 h-100">
                        
                        <!-- Panel 1: Conversations List -->
                        <div class="col-12 col-md-5 col-lg-4 border-end border-secondary border-opacity-10 d-flex flex-column h-100 {{ request()->has('c') ? 'd-none d-md-flex' : 'd-flex' }}" id="conversationsListPanel">
                            
                            <!-- Search Conversations Bar -->
                            <div class="p-3 border-bottom border-secondary border-opacity-10">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text dark-search-addon" style="background: #081D33; border-color: rgba(255,255,255,0.08);">
                                        <i class="bi bi-search text-secondary"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control dark-filter-input border-start-0" 
                                           id="chatSearchInput" 
                                           placeholder="Search conversations..."
                                           style="font-size: 0.82rem;">
                                </div>
                            </div>

                            <!-- List of Threads -->
                            <div class="overflow-auto flex-grow-1 p-2" id="threadsList" style="overflow-x: hidden !important;">
                                @forelse($conversations as $conv)
                                    @php $isActive = ($activeConversation['id'] == $conv['id']); @endphp
                                    <a href="{{ url('/messages?c=' . $conv['id']) }}" 
                                       class="d-flex align-items-center gap-2 p-2 p-lg-3 rounded-3 text-decoration-none mb-1 conversation-item {{ $isActive ? 'active-thread' : '' }}"
                                       data-name="{{ strtolower($conv['user']['name']) }}"
                                       data-item="{{ strtolower($conv['listing']['title']) }}"
                                       style="background: {{ $isActive ? 'rgba(73, 209, 125, 0.08)' : 'transparent' }}; border: 1px solid {{ $isActive ? 'rgba(73, 209, 125, 0.3)' : 'transparent' }}; transition: background 0.15s ease; max-width: 100%; overflow: hidden;">
                                        
                                        <!-- User Avatar -->
                                        <div class="position-relative flex-shrink-0" style="width: 42px; height: 42px;">
                                            <img src="{{ $conv['user']['avatar'] }}" 
                                                 alt="{{ $conv['user']['name'] }}" 
                                                 class="rounded-circle object-fit-cover w-100 h-100" 
                                                 style="border: 1.5px solid rgba(255,255,255,0.1);">
                                            @if($conv['user']['online'])
                                                <span class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle" style="width: 10px; height: 10px;"></span>
                                            @endif
                                        </div>

                                        <!-- Thread Content -->
                                        <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <h6 class="fw-bold text-white mb-0 text-truncate" style="font-size: 0.86rem;">
                                                    {{ $conv['user']['name'] }}
                                                </h6>
                                                <span class="text-secondary small flex-shrink-0" style="font-size: 0.7rem;">{{ $conv['last_time'] }}</span>
                                            </div>

                                            <div class="small text-success text-truncate mb-1 d-block" style="font-size: 0.74rem;">
                                                <i class="bi bi-tag-fill me-1 opacity-75"></i>{{ $conv['listing']['title'] }}
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between gap-1">
                                                <p class="small text-secondary mb-0 text-truncate" style="font-size: 0.76rem;">
                                                    {{ $conv['last_message'] }}
                                                </p>
                                                @if(!empty($conv['unread']))
                                                    <span class="badge bg-danger rounded-circle p-1 flex-shrink-0" style="width: 8px; height: 8px;" title="Unread message"></span>
                                                @endif
                                            </div>
                                        </div>

                                    </a>
                                @empty
                                    <div class="text-center p-4 text-secondary small">
                                        No conversations yet.
                                    </div>
                                @endforelse
                            </div>

                        </div>

                        <!-- Panel 2: Active Chat View -->
                        <div class="col-12 col-md-7 col-lg-8 d-flex flex-column h-100 {{ request()->has('c') ? 'd-flex' : 'd-none d-md-flex' }}" id="chatPanel" style="background: #081D33;">
                            
                            <!-- Chat Top Bar & Ad Context Banner -->
                            <div class="p-3 border-bottom border-secondary border-opacity-10 d-flex align-items-center justify-content-between gap-2" style="background: #0D243C;">
                                <div class="d-flex align-items-center gap-3 min-w-0">
                                    <!-- Mobile Back to threads list -->
                                    <a href="{{ url('/messages') }}" class="btn btn-sm btn-dark d-md-none text-secondary">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>

                                    <img src="{{ $activeConversation['user']['avatar'] }}" 
                                         alt="{{ $activeConversation['user']['name'] }}" 
                                         class="rounded-circle object-fit-cover flex-shrink-0" 
                                         style="width: 42px; height: 42px; border: 1.5px solid rgba(255,255,255,0.1);">
                                    
                                    <div class="min-w-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="fw-bold text-white mb-0 text-truncate" style="font-size: 0.92rem;">
                                                {{ $activeConversation['user']['name'] }}
                                            </h6>
                                            @if($activeConversation['user']['verified'])
                                                <i class="bi bi-patch-check-fill text-success" title="Verified User" style="font-size: 0.85rem;"></i>
                                            @endif
                                        </div>
                                        <span class="small text-secondary" style="font-size: 0.75rem;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $activeConversation['user']['location'] }} • {{ $activeConversation['user']['online'] ? 'Online now' : 'Active recently' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Listing Quick Pill -->
                                <a href="{{ url('/listing/' . $activeConversation['listing']['id']) }}" 
                                   class="d-none d-sm-flex align-items-center gap-2 p-1 pe-3 rounded-pill text-decoration-none border border-secondary border-opacity-25"
                                   style="background: #081D33; font-size: 0.78rem;">
                                    <img src="{{ $activeConversation['listing']['image'] }}" class="rounded-circle object-fit-cover" style="width: 26px; height: 26px;">
                                    <span class="text-white text-truncate" style="max-width: 140px;">{{ $activeConversation['listing']['title'] }}</span>
                                    <strong class="text-success">{{ $activeConversation['listing']['price'] }}</strong>
                                </a>
                            </div>

                            <!-- Messages Stream Box -->
                            <div class="p-3 p-md-4 overflow-auto flex-grow-1 d-flex flex-column gap-3" id="messagesStream">
                                
                                <!-- Safety Tip Pill -->
                                <div class="text-center my-2">
                                    <span class="badge bg-dark border border-secondary border-opacity-25 text-secondary px-3 py-2 rounded-pill small" style="font-size: 0.75rem;">
                                        <i class="bi bi-shield-lock-fill text-success me-1"></i> Always meet in a public place. Never send advance deposits.
                                    </span>
                                </div>

                                @foreach($activeConversation['messages'] as $msg)
                                    @php $isMe = ($msg['sender'] === 'me'); @endphp
                                    <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}">
                                        <div class="p-3 rounded-4 message-bubble {{ $isMe ? 'my-bubble' : 'their-bubble' }}" 
                                             style="max-width: 80%; font-size: 0.88rem; line-height: 1.45; {{ $isMe ? 'background: #49D17D; color: #06182B; font-weight: 500; border-bottom-right-radius: 4px !important;' : 'background: #0D243C; color: #E2E8F0; border: 1px solid rgba(255,255,255,0.08); border-bottom-left-radius: 4px !important;' }}">
                                            {{ $msg['text'] }}
                                        </div>
                                        <span class="text-secondary small mt-1 px-1" style="font-size: 0.7rem;">{{ $msg['time'] }}</span>
                                    </div>
                                @endforeach

                            </div>

                            <!-- Bottom Input & Send Box -->
                            <div class="p-3 border-top border-secondary border-opacity-10" style="background: #0D243C;">
                                <form id="chatSendForm" onsubmit="event.preventDefault(); sendChatMessage();">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control dark-filter-input border-0 py-2" 
                                               id="chatInput" 
                                               placeholder="Type your message to {{ $activeConversation['user']['name'] }}..." 
                                               autocomplete="off"
                                               required
                                               style="background: #081D33 !important; font-size: 0.88rem;">
                                        <button class="btn btn-theme-primary px-4 d-inline-flex align-items-center gap-1 fw-semibold" type="submit" id="btnSendMessage">
                                            <span>Send</span>
                                            <i class="bi bi-send-fill ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const text = input ? input.value.trim() : '';
    if (!text) return;

    const stream = document.getElementById('messagesStream');
    if (stream) {
        const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const bubble = document.createElement('div');
        bubble.className = 'd-flex flex-column align-items-end';
        bubble.innerHTML = `
            <div class="p-3 rounded-4 message-bubble my-bubble" 
                 style="max-width: 80%; font-size: 0.88rem; line-height: 1.45; background: #49D17D; color: #06182B; font-weight: 500; border-bottom-right-radius: 4px !important;">
                ${escapeHtml(text)}
            </div>
            <span class="text-secondary small mt-1 px-1" style="font-size: 0.7rem;">${timeNow}</span>
        `;
        stream.appendChild(bubble);
        stream.scrollTop = stream.scrollHeight;
    }

    input.value = '';

    fetch(`{{ url('/messages/' . $activeConversation['id'] . '/reply') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: text })
    }).catch(() => {});
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Search Filter in Left Threads List
const chatSearchInput = document.getElementById('chatSearchInput');
if (chatSearchInput) {
    chatSearchInput.addEventListener('input', function(e) {
        const q = e.target.value.trim().toLowerCase();
        document.querySelectorAll('.conversation-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const itemName = item.getAttribute('data-item') || '';
            if (!q || name.includes(q) || itemName.includes(q)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
}
</script>
@endpush
@endsection
