@extends('frontend.account.layout', ['title' => 'Messages & Inbox | Bontrouver Canadian Classifieds', 'metaDescription' => 'Chat with buyers and sellers in real time, negotiate deals, and manage marketplace conversations.', 'activeNav' => 'messages'])

@section('account_content')
                <!-- Messaging Card Container -->
<!-- Scoped Messaging Container Styles -->
<style>
    .chat-layout-wrapper {
        background: #0D243C;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        height: 700px;
        max-height: calc(100vh - 200px);
        min-height: 540px;
        display: flex;
        overflow: hidden;
        position: relative;
    }

    .chat-threads-col {
        width: 340px;
        min-width: 280px;
        max-width: 360px;
        height: 100%;
        display: flex;
        flex-direction: column;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        background: #0D243C;
        flex-shrink: 0;
    }

    .chat-view-col {
        flex: 1 1 0%;
        min-width: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #081D33;
        position: relative;
    }

    .chat-header-bar {
        flex-shrink: 0;
        padding: 12px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: #0D243C;
        z-index: 5;
    }

    .chat-messages-stream {
        flex: 1 1 0%;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-input-bar {
        flex-shrink: 0;
        padding: 14px 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background: #0D243C;
        position: relative;
        z-index: 10;
    }

    .conversation-item.active-thread {
        background: rgba(73, 209, 125, 0.12) !important;
        border-color: rgba(73, 209, 125, 0.35) !important;
    }

    .conversation-item:hover {
        background: rgba(255, 255, 255, 0.04);
    }

    @media (max-width: 767.98px) {
        .chat-layout-wrapper {
            height: calc(100vh - 170px);
            min-height: 480px;
        }
        .chat-threads-col {
            width: 100%;
            max-width: 100%;
            border-right: none;
        }
    }
</style>

<!-- Messaging Main Container -->
<div class="dark-surface-card chat-layout-wrapper">
    
    <!-- Panel 1: Conversations List -->
    <div class="chat-threads-col {{ request()->has('c') ? 'd-none d-md-flex' : 'd-flex' }}" id="conversationsListPanel">
        
        <!-- Search Conversations Bar -->
        <div class="p-3 border-bottom border-secondary border-opacity-10 flex-shrink-0">
            <div class="input-group input-group-sm">
                <span class="input-group-text dark-search-addon" style="background: #081D33; border-color: rgba(255,255,255,0.08);">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" 
                       class="form-control dark-filter-input border-start-0" 
                       id="chatSearchInput" 
                       placeholder="Search conversations..."
                       style="font-size: 0.84rem; background: #081D33 !important;">
            </div>
        </div>

        <!-- List of Threads -->
        <div class="overflow-auto flex-grow-1 p-2" id="threadsList" style="overflow-x: hidden !important;">
            @forelse($conversations as $conv)
                @php $isActive = ($activeConversation && $activeConversation['id'] == $conv['id']); @endphp
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
                             style="border: 1.5px solid rgba(255,255,255,0.1);"
                             onerror="this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80'">
                        @if($conv['user']['online'])
                            <span class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle" style="width: 10px; height: 10px;"></span>
                        @endif
                    </div>

                    <!-- Thread Content -->
                    <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                            <h6 class="fw-bold text-white mb-0 text-truncate" style="font-size: 0.88rem;">
                                {{ $conv['user']['name'] }}
                            </h6>
                            <span class="text-secondary small flex-shrink-0" style="font-size: 0.7rem;">{{ $conv['last_time'] }}</span>
                        </div>

                        @if(!empty($conv['listing']['id']) && ($conv['listing']['title'] ?? '') !== 'Deleted Listing')
                            <div class="small text-secondary text-truncate mb-1 d-block" style="font-size: 0.74rem;">
                                <i class="bi bi-tag-fill me-1 opacity-75 text-success"></i>{{ $conv['listing']['title'] }}
                            </div>
                        @endif

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
                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                    No conversations yet.
                </div>
            @endforelse
        </div>

    </div>

    <!-- Panel 2: Active Chat View -->
    <div class="chat-view-col {{ request()->has('c') ? 'd-flex' : 'd-none d-md-flex' }}" id="chatPanel">
        @if($activeConversation)
        <!-- Chat Top Bar & Member Info -->
        <div class="chat-header-bar d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <!-- Mobile Back to threads list -->
                <a href="{{ url('/messages') }}" class="btn btn-sm btn-dark d-md-none text-secondary">
                    <i class="bi bi-chevron-left"></i>
                </a>

                @if(!empty($activeConversation['user']['avatar']))
                    <img src="{{ $activeConversation['user']['avatar'] }}" 
                         alt="{{ $activeConversation['user']['name'] ?? 'User' }}" 
                         class="rounded-circle object-fit-cover flex-shrink-0" 
                         style="width: 42px; height: 42px; border: 1.5px solid rgba(255,255,255,0.1);"
                         onerror="this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80'">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-dark fw-bold"
                         style="width: 42px; height: 42px; background: #49D17D; font-size: 0.9rem;">
                        {{ strtoupper(substr($activeConversation['user']['name'] ?? 'U', 0, 1)) }}
                    </div>
                @endif
                
                <div class="overflow-hidden">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h6 class="fw-bold text-white mb-0 text-truncate" style="font-size: 0.94rem;">
                            {{ $activeConversation['user']['name'] ?? 'User' }}
                        </h6>
                        @if(!empty($activeConversation['user']['verified']))
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 0.68rem; padding: 2px 6px;">
                                <i class="bi bi-shield-check me-1"></i> Verified
                            </span>
                        @endif
                        @if(!empty($activeConversation['user']['member_tier']))
                            @php $chatTier = $activeConversation['user']['member_tier']; @endphp
                            <span class="badge {{ $chatTier['badge_class'] ?? 'bg-secondary' }} border border-secondary border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                <span class="me-0.5">{{ $chatTier['icon'] }}</span> {{ $chatTier['name'] }}
                            </span>
                        @endif
                    </div>
                    <span class="small text-secondary text-truncate d-block" style="font-size: 0.75rem;">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $activeConversation['user']['location'] ?? 'Canada' }} • {{ !empty($activeConversation['user']['online']) ? 'Online now' : 'Active recently' }}
                    </span>
                </div>
            </div>

            @if(!empty($activeConversation['user']['id']))
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="{{ route('profile.view', ['id' => $activeConversation['user']['id']]) }}" target="_blank" class="btn btn-sm btn-theme-outline-secondary rounded-pill px-3 py-1 text-nowrap d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-sm-inline">Profile</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Messages Stream Box -->
        <div class="chat-messages-stream" id="messagesStream">
            
            <!-- Safety Tip Pill -->
            <div class="text-center my-1">
                <span class="badge bg-dark border border-secondary border-opacity-25 text-secondary px-3 py-1.5 rounded-pill small" style="font-size: 0.75rem;">
                    <i class="bi bi-shield-lock-fill text-success me-1"></i> Always meet in a public place. Never send advance deposits.
                </span>
            </div>

            @foreach($activeConversation['messages'] as $msg)
                @php $isMe = ($msg['sender'] === 'me'); @endphp
                <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }} mb-1">
                    <div class="p-3 rounded-4 message-bubble {{ $isMe ? 'my-bubble' : 'their-bubble' }}" 
                         style="max-width: 80%; font-size: 0.88rem; line-height: 1.45; {{ $isMe ? 'background: #49D17D; color: #06182B; font-weight: 500; border-bottom-right-radius: 4px !important;' : 'background: #0D243C; color: #E2E8F0; border: 1px solid rgba(255,255,255,0.08); border-bottom-left-radius: 4px !important;' }}">
                        @if(!empty($msg['attachments']))
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach($msg['attachments'] as $att)
                                    @php
                                        $attUrl = is_array($att) ? ($att['url'] ?? '') : (string) $att;
                                        $attType = is_array($att) ? ($att['type'] ?? 'file') : 'file';
                                        $attName = is_array($att) ? ($att['name'] ?? basename($attUrl)) : basename($attUrl);
                                    @endphp
                                    @if($attType === 'image')
                                        <div class="position-relative d-inline-block rounded-3 overflow-hidden" style="border: 1px solid rgba(255,255,255,0.12); background: #081D33;">
                                            <a href="javascript:void(0)" onclick="openImageModal('{{ $attUrl }}')">
                                                <img src="{{ $attUrl }}" alt="{{ $attName }}" class="img-fluid rounded-3" style="max-height: 160px; max-width: 220px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                            </a>
                                            <a href="{{ $attUrl }}" download="{{ $attName }}" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-1 rounded-circle border border-secondary border-opacity-25 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;" title="Download">
                                                <i class="bi bi-download" style="font-size: 0.7rem;"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="p-2 bg-light bg-opacity-10 rounded-3 d-flex align-items-center gap-2 border border-white-10">
                                            <i class="bi bi-file-earmark-fill fs-4 text-success"></i>
                                            <div class="min-w-0 me-2">
                                                <div class="text-white small fw-semibold text-truncate" style="font-size: 0.8rem; max-width: 180px;" title="{{ $attName }}">{{ $attName }}</div>
                                            </div>
                                            <a href="{{ $attUrl }}" download="{{ $attName }}" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size: 0.75rem;">Download</a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($msg['text']))
                            <div class="text-break">{{ $msg['text'] }}</div>
                        @endif
                    </div>
                    <span class="text-secondary small mt-1 px-1" style="font-size: 0.7rem;">{{ $msg['time'] }}</span>
                </div>
            @endforeach

        </div>

        <!-- Bottom Input & Send Box (Guaranteed Pinned) -->
        <div class="chat-input-bar" id="chatInputBar">
            <div id="attachmentPreview" class="d-none mb-2 p-2 bg-dark rounded border border-secondary border-opacity-25 d-flex gap-2 overflow-x-auto" style="max-width: 100%; white-space: nowrap;">
                <!-- Previews will be injected here via JS -->
            </div>
            <form id="chatSendForm" onsubmit="event.preventDefault(); sendChatMessage();" enctype="multipart/form-data" class="m-0">
                <div class="input-group align-items-center">
                    <button class="btn btn-dark text-secondary border-0 px-3 d-flex align-items-center justify-content-center" type="button" onclick="document.getElementById('chatAttachment').click();" style="background: #081D33; height: 46px;" title="Attach photos or documents">
                        <i class="bi bi-paperclip fs-5"></i>
                    </button>
                    <input type="file" id="chatAttachment" class="d-none" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" onchange="handleAttachmentSelect(this)">
                    <input type="text" 
                           class="form-control dark-filter-input border-0 px-3" 
                           id="chatInput" 
                           placeholder="Type your message to {{ $activeConversation['user']['name'] }}..." 
                           autocomplete="off"
                           style="background: #081D33 !important; font-size: 0.9rem; height: 46px; color: #FFFFFF !important;">
                    <button class="btn btn-theme-primary px-4 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm" type="submit" id="btnSendMessage" style="height: 46px;">
                        <span>Send</span>
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </form>
        </div>
        @else
            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-secondary p-4 text-center">
                <div class="rounded-circle p-4 mb-3" style="background: #0D243C; border: 1px solid rgba(255,255,255,0.08);">
                    <i class="bi bi-chat-square-dots-fill text-success" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="text-white fw-bold mb-1">No Conversation Selected</h5>
                <p class="small text-secondary mb-3" style="max-width: 320px;">Select a conversation from the left thread list or contact a seller on any listing to start chatting.</p>
                <a href="{{ route('listings.index') }}" class="btn btn-theme-primary btn-sm px-4 py-2 rounded-pill">
                    <i class="bi bi-search me-1"></i> Browse Marketplace
                </a>
            </div>
        @endif
    </div>

</div>

<!-- Full Screen Image Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0 position-relative">
                <img id="modalImagePreview" src="" class="img-fluid rounded shadow-lg" style="max-height: 85vh; object-fit: contain;">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <a id="modalDownloadBtn" href="" download class="btn btn-theme-primary px-4 rounded-pill shadow">
                    <i class="bi bi-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedFiles = [];

function handleAttachmentSelect(input) {
    if (input.files && input.files.length > 0) {
        // Prevent adding more than 5 total files
        if (selectedFiles.length + input.files.length > 5) {
            showToast('You can only attach up to 5 files per message.', 'warning');
            input.value = '';
            return;
        }

        Array.from(input.files).forEach(file => {
            selectedFiles.push(file);
        });
        
        input.value = ''; // Reset input to allow selecting same file again if needed
        renderPreviews();
    }
}

function renderPreviews() {
    const previewContainer = document.getElementById('attachmentPreview');
    previewContainer.innerHTML = '';

    if (selectedFiles.length === 0) {
        previewContainer.classList.add('d-none');
        return;
    }

    selectedFiles.forEach((file, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'position-relative d-inline-block bg-secondary bg-opacity-25 rounded p-1';
        itemDiv.style.minWidth = '50px';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center shadow';
        removeBtn.style.width = '18px';
        removeBtn.style.height = '18px';
        removeBtn.style.transform = 'translate(30%, -30%)';
        removeBtn.innerHTML = '<i class="bi bi-x" style="font-size: 0.7rem;"></i>';
        removeBtn.onclick = () => removeFile(index);

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.className = 'rounded object-fit-cover';
            img.style.width = '50px';
            img.style.height = '50px';
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
            itemDiv.appendChild(img);
        } else {
            itemDiv.innerHTML = `<div class="d-flex flex-column align-items-center justify-content-center text-white" style="width: 50px; height: 50px;"><i class="bi bi-file-earmark-fill fs-5 text-success"></i></div>`;
            // Re-attach the button since innerHTML wiped it
        }
        itemDiv.appendChild(removeBtn);
        previewContainer.appendChild(itemDiv);
    });

    previewContainer.classList.remove('d-none');
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
}

function openImageModal(url) {
    document.getElementById('modalImagePreview').src = url;
    document.getElementById('modalDownloadBtn').href = url;
    const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
    modal.show();
}

function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const text = input ? input.value.trim() : '';
    
    if (!text && selectedFiles.length === 0) return;

    // We rely on the server response to render our own message properly if there's a file
    // so we don't optimistically render it unless it's just text
    if (selectedFiles.length === 0) {
        const stream = document.getElementById('messagesStream');
        if (stream) {
            const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const bubble = document.createElement('div');
            bubble.className = 'd-flex flex-column align-items-end mb-2';
            bubble.innerHTML = `
                <div class="p-3 rounded-4 message-bubble my-bubble" 
                     style="max-width: 80%; font-size: 0.88rem; line-height: 1.45; background: #49D17D; color: #06182B; font-weight: 500; border-bottom-right-radius: 4px !important;">
                    <div class="text-break mt-1">${escapeHtml(text)}</div>
                </div>
                <span class="text-secondary small mt-1 px-1" style="font-size: 0.7rem;">${timeNow}</span>
            `;
            stream.appendChild(bubble);
            stream.scrollTop = stream.scrollHeight;
        }
    }

    const formData = new FormData();
    if (text) formData.append('message', text);
    
    selectedFiles.forEach((f) => {
        formData.append('attachments[]', f);
    });

    input.value = '';
    selectedFiles = [];
    renderPreviews();

    fetch(`{{ $activeConversation ? url('/messages/' . $activeConversation['id'] . '/reply') : '#' }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.message.attachments && data.message.attachments.length > 0) {
            // Append the message returned by the server because it has the attachment url
            const stream = document.getElementById('messagesStream');
            if (stream) {
                let attachmentHtml = '<div class="d-flex flex-wrap gap-2 mb-2">';
                data.message.attachments.forEach(att => {
                    const attUrl = typeof att === 'string' ? att : (att.url || '');
                    const safeUrl = resolveSafeUrl(attUrl);
                    const attType = typeof att === 'object' && att.type ? att.type : (/\.(jpg|jpeg|png|webp|gif|svg)$/i.test(attUrl) ? 'image' : 'file');
                    const attName = escapeHtml(typeof att === 'object' && att.name ? att.name : (attUrl.split('/').pop() || 'Attachment'));

                    if (attType === 'image') {
                        attachmentHtml += `
                            <div class="position-relative d-inline-block rounded-3 overflow-hidden" style="border: 1px solid rgba(255,255,255,0.12); background: #081D33;">
                                <a href="javascript:void(0)" onclick="openImageModal('${safeUrl}')">
                                    <img src="${safeUrl}" alt="${attName}" class="img-fluid rounded-3" style="max-height: 160px; max-width: 220px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                </a>
                                <a href="${safeUrl}" download="${attName}" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-1 rounded-circle border border-secondary border-opacity-25 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;" title="Download">
                                    <i class="bi bi-download" style="font-size: 0.7rem;"></i>
                                </a>
                            </div>
                        `;
                    } else {
                        attachmentHtml += `
                            <div class="p-2 bg-light bg-opacity-10 rounded-3 d-flex align-items-center gap-2 border border-white-10">
                                <i class="bi bi-file-earmark-fill fs-4 text-success"></i>
                                <div class="min-w-0 me-2">
                                    <div class="text-white small fw-semibold text-truncate" style="font-size: 0.8rem; max-width: 180px;" title="${attName}">${attName}</div>
                                </div>
                                <a href="${safeUrl}" download="${attName}" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size: 0.75rem;">Download</a>
                            </div>
                        `;
                    }
                });
                attachmentHtml += '</div>';

                const bubble = document.createElement('div');
                bubble.className = 'd-flex flex-column align-items-end mb-2';
                bubble.innerHTML = `
                    <div class="p-3 rounded-4 message-bubble my-bubble" 
                         style="max-width: 80%; font-size: 0.88rem; line-height: 1.45; background: #49D17D; color: #06182B; font-weight: 500; border-bottom-right-radius: 4px !important;">
                        ${attachmentHtml}
                        ${data.message.text ? `<div class="text-break mt-1">${escapeHtml(data.message.text)}</div>` : ''}
                    </div>
                    <span class="text-secondary small mt-1 px-1" style="font-size: 0.7rem;">${data.message.time}</span>
                `;
                stream.appendChild(bubble);
                stream.scrollTop = stream.scrollHeight;
            }
        }
    })
    .catch(() => {});
}

function resolveSafeUrl(url) {
    if (!url) return '';
    return url.replace(/^https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?(\/.*)$/i, '$1');
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

// Laravel Echo Integration for Real-time Messages
document.addEventListener('DOMContentLoaded', function () {
    @if($activeConversation)
        const currentUserId = {{ auth()->id() }};
        const conversationId = {{ $activeConversation['id'] }};
        const stream = document.getElementById('messagesStream');
        
        // Scroll to the bottom on load
        if (stream) {
            stream.scrollTop = stream.scrollHeight;
        }

        if (window.Echo) {
            // 1. Listen to the active conversation to append messages
            window.Echo.private('conversation.' + conversationId)
                .listen('MessageSent', (e) => {
                    if (e.sender_id !== currentUserId && stream) {
                        const timeNow = e.time;
                        let attachmentHtml = '';
                        if (e.attachments && e.attachments.length > 0) {
                            attachmentHtml = '<div class="d-flex flex-wrap gap-2 mb-2">';
                            e.attachments.forEach(att => {
                                const attUrl = typeof att === 'string' ? att : (att.url || '');
                                const safeUrl = resolveSafeUrl(attUrl);
                                const attType = typeof att === 'object' && att.type ? att.type : (/\.(jpg|jpeg|png|webp|gif|svg)$/i.test(attUrl) ? 'image' : 'file');
                                const attName = escapeHtml(typeof att === 'object' && att.name ? att.name : (attUrl.split('/').pop() || 'Attachment'));

                                if (attType === 'image') {
                                    attachmentHtml += `
                                        <div class="position-relative d-inline-block rounded-3 overflow-hidden" style="border: 1px solid rgba(255,255,255,0.12); background: #081D33;">
                                            <a href="javascript:void(0)" onclick="openImageModal('${safeUrl}')">
                                                <img src="${safeUrl}" alt="${attName}" class="img-fluid rounded-3" style="max-height: 160px; max-width: 220px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                            </a>
                                            <a href="${safeUrl}" download="${attName}" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-1 rounded-circle border border-secondary border-opacity-25 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;" title="Download">
                                                <i class="bi bi-download" style="font-size: 0.7rem;"></i>
                                            </a>
                                        </div>
                                    `;
                                } else {
                                    attachmentHtml += `
                                        <div class="p-2 bg-light bg-opacity-10 rounded-3 d-flex align-items-center gap-2 border border-white-10">
                                            <i class="bi bi-file-earmark-fill fs-4 text-success"></i>
                                            <div class="min-w-0 me-2">
                                                <div class="text-white small fw-semibold text-truncate" style="font-size: 0.8rem; max-width: 180px;" title="${attName}">${attName}</div>
                                            </div>
                                            <a href="${safeUrl}" download="${attName}" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size: 0.75rem;">Download</a>
                                        </div>
                                    `;
                                }
                            });
                            attachmentHtml += '</div>';
                        }

                        const bubble = document.createElement('div');
                        bubble.className = 'd-flex gap-2 mb-3';
                        bubble.innerHTML = `
                            <img src="${e.sender_avatar || '{{ asset('images/avatar-placeholder.png') }}'}" 
                                 class="rounded-circle object-fit-cover mt-auto" 
                                 style="width: 28px; height: 28px;" alt="User">
                            <div class="d-flex flex-column align-items-start">
                                <div class="p-3 rounded-4 message-bubble" 
                                     style="max-width: 80%; font-size: 0.88rem; line-height: 1.45; background: #081D33; color: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.08); border-bottom-left-radius: 4px !important;">
                                    ${attachmentHtml}
                                    ${e.body ? `<div class="text-break mt-1">${escapeHtml(e.body)}</div>` : ''}
                                </div>
                                <span class="text-secondary small mt-1 px-1" style="font-size: 0.7rem;">${timeNow}</span>
                            </div>
                        `;
                        stream.appendChild(bubble);
                        stream.scrollTop = stream.scrollHeight;
                    }
                });
            
            // 2. Listen to the user's global channel to refresh the sidebar if a new conversation/message arrives
            window.Echo.private('App.Models.User.' + currentUserId)
                .listen('MessageSent', (e) => {
                    if (e.conversation_id !== conversationId) {
                        // Refresh just the sidebar via AJAX
                        fetch(window.location.href)
                            .then(r => r.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newSidebar = doc.getElementById('threadsList');
                                if (newSidebar) {
                                    document.getElementById('threadsList').innerHTML = newSidebar.innerHTML;
                                }
                            });
                    }
                });
        }
    @endif
});
</script>
@endpush
