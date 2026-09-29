@php
    $isGuest = !auth()->check();
    $currentUser = auth()->user();
@endphp

<!-- Bon Trouver Professional Support & Live Chat Widget -->
<div id="btSupportWidget" class="bt-support-widget-container">
    <!-- Mini Teaser Popover (Greeting Banner) -->
    <div id="btSupportTeaser" class="bt-support-teaser shadow-lg d-none">
        <div class="d-flex align-items-center gap-2">
            <div class="bt-teaser-avatar">
                <i class="bi bi-headset"></i>
                <span class="bt-teaser-dot"></span>
            </div>
            <div class="bt-teaser-content pe-1 flex-grow-1">
                <div class="bt-teaser-title">Need help with something? 🇨🇦</div>
                <div class="bt-teaser-sub">Our Canadian support team is online!</div>
            </div>
            <button type="button" id="btCloseTeaserBtn" class="bt-teaser-close" aria-label="Dismiss greeting">
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>

    <!-- Floating Trigger Bubble -->
    <button type="button" id="btSupportTrigger" class="bt-support-trigger-btn shadow-lg" aria-label="Open Live Support Widget">
        <div class="bt-support-trigger-inner">
            <span class="bt-support-icon-chat">
                <i class="bi bi-chat-dots-fill"></i>
            </span>
            <span class="bt-support-icon-close d-none">
                <i class="bi bi-x-lg"></i>
            </span>
            <span id="btSupportUnreadBadge" class="bt-support-badge d-none">0</span>
        </div>
        <span class="bt-support-pulse"></span>
    </button>

    <!-- Support Chat Window Shell -->
    <div id="btSupportWindow" class="bt-support-window shadow-2xl d-none">
        
        <!-- Header Bar -->
        <div class="bt-support-header d-flex align-items-center justify-content-between px-3 py-2">
            <div class="d-flex align-items-center gap-2">
                <div class="bt-support-avatar-wrapper position-relative">
                    <div class="bt-support-avatar-circle">
                        <i class="bi bi-headset"></i>
                    </div>
                    <span class="bt-support-online-dot" title="Support Team Online"></span>
                </div>
                <div class="text-truncate" style="max-width: 210px;">
                    <div class="d-flex align-items-center gap-1">
                        <h6 class="mb-0 fw-bold text-white fs-6 text-truncate" id="btHeaderTitle">Bon Trouver Concierge</h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0 px-1" style="font-size: 0.65rem;">Live 🇨🇦</span>
                    </div>
                    <small class="text-white-50 d-block text-truncate" id="btHeaderSubtitle" style="font-size: 0.72rem;">Usually replies in ~5 mins</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" id="btSupportMinimizeBtn" class="btn btn-sm btn-icon text-white-50 hover-text-white p-1" title="Minimize">
                    <i class="bi bi-dash-lg"></i>
                </button>
                <button type="button" id="btSupportCloseBtn" class="btn btn-sm btn-icon text-white-50 hover-text-white p-1" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <!-- Sleek Dual Tabs: Live Chat vs FAQ & Help -->
        <div class="bt-tabs-bar px-3 pt-2 pb-2 d-flex gap-2 border-bottom border-white-08">
            <button type="button" id="btTabBtnChat" class="bt-tab-btn active flex-grow-1">
                <i class="bi bi-chat-dots-fill me-1"></i> Live Chat
            </button>
            <button type="button" id="btTabBtnFaq" class="bt-tab-btn flex-grow-1">
                <i class="bi bi-question-circle-fill me-1"></i> FAQ & Help
            </button>
        </div>

        <!-- ================= TAB 1: LIVE CHAT ================= -->
        <div id="btTabContentChat" class="bt-tab-content active d-flex flex-column">
            @if($isGuest)
                <!-- Guest Gate State -->
                <div class="bt-guest-chat-container p-4 d-flex flex-column align-items-center justify-content-center text-center flex-grow-1">
                    <div class="bt-guest-icon-box mb-3">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2" style="font-size: 1.1rem;">Account Required to Chat</h5>
                    <p class="text-white-50 mb-4" style="font-size: 0.82rem; line-height: 1.5; max-width: 290px;">
                        To protect our Canadian community from spam and offer personalized assistance, please log in to start a live support ticket.
                    </p>

                    <div class="d-flex flex-column gap-2 w-100 mb-3" style="max-width: 280px;">
                        <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-success fw-bold rounded-pill py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" style="font-size: 0.88rem;">
                            <i class="bi bi-box-arrow-in-right"></i> Log In to Chat
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light rounded-pill py-2 text-white border-white-20 hover-bg-white-10 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem; background: rgba(255,255,255,0.04);">
                            <i class="bi bi-person-plus"></i> Create Free Account
                        </a>
                    </div>

                    <button type="button" id="btGuestSwitchToFaqBtn" class="btn btn-link text-success text-decoration-none p-0" style="font-size: 0.8rem;">
                        <i class="bi bi-question-circle me-1"></i> Browse FAQs without account &rarr;
                    </button>
                </div>
            @else
                <!-- Authenticated Live Chat Area -->
                

                <!-- Messages Stream Container -->
                <div id="btMessagesContainer" class="bt-messages-container flex-grow-1 p-3">
                    <div class="text-center py-4 text-white-50" id="btMessagesLoading">
                        <span class="spinner-border spinner-border-sm text-success me-1"></span> Connecting to support...
                    </div>
                </div>

                <!-- Typing / Sending Indicator -->
                <div id="btTypingIndicator" class="px-3 py-1 d-none">
                    <div class="bt-typing-bubble d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill">
                        <span class="bt-dot"></span>
                        <span class="bt-dot"></span>
                        <span class="bt-dot"></span>
                        <span class="text-white-50 ms-1" style="font-size: 0.7rem;">Sending message...</span>
                    </div>
                </div>

                <!-- Attachment Preview Box -->
                <div id="btAttachmentPreview" class="px-3 py-2 border-top border-white-10 bg-dark-card d-none">
                    <div id="btAttachmentPreviewList" class="d-flex flex-wrap gap-2"></div>
                </div>

                <!-- Chat Composer Form -->
                <div class="bt-chat-input-area p-2 border-top border-white-10">
                    <form id="btSupportMessageForm" class="d-flex align-items-end gap-1">
                        <input type="file" id="btChatFileInput" class="d-none" accept="image/*,.pdf,.doc,.docx,.txt,.rtf,.xls,.xlsx,.csv" multiple>
                        
                        <button type="button" id="btAttachFileBtn" class="btn btn-icon btn-sm text-white-50 hover-text-white rounded-circle p-2" title="Attach image or document (PDF, Doc, etc.)" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); width: 38px; height: 38px;">
                            <i class="bi bi-paperclip fs-6"></i>
                        </button>

                        <div class="flex-grow-1 position-relative">
                            <textarea id="btChatInput" rows="1" class="form-control bt-chat-textarea" placeholder="Type your message..." maxlength="3000"></textarea>
                        </div>

                        <button type="submit" id="btSendMessageBtn" class="btn btn-success btn-sm rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Send Message">
                            <i class="bi bi-send-fill" style="font-size: 0.85rem; margin-left: 2px;"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Image Lightbox Modal for Widget -->
        <div id="btLightboxOverlay" class="bt-lightbox-overlay d-none" onclick="if(event.target === this) closeBtLightbox();">
            <div class="bt-lightbox-content">
                <div class="bt-lightbox-header d-flex align-items-center justify-content-between p-2">
                    <span id="btLightboxTitle" class="text-white text-truncate small fw-semibold" style="max-width: 75%;">Image Preview</span>
                    <div class="d-flex align-items-center gap-2">
                        <a id="btLightboxDownloadBtn" href="#" download class="btn btn-sm btn-success py-1 px-2 text-white" title="Download Image">
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                        <button type="button" class="btn btn-sm btn-link text-white-50 p-0" onclick="closeBtLightbox()">
                            <i class="bi bi-x-lg fs-6"></i>
                        </button>
                    </div>
                </div>
                <div class="bt-lightbox-body text-center p-2">
                    <img id="btLightboxImg" src="" alt="Full view" class="img-fluid rounded-3" style="max-height: 75vh; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: FAQ & HELP ================= -->
        <div id="btTabContentFaq" class="bt-tab-content d-none flex-column">
            <!-- FAQ Accordion List -->
            <div class="p-3 overflow-y-auto flex-grow-1 bt-faq-scroll">
                <div class="bt-faq-list" id="btFaqAccordionList">
                    <div class="text-center py-4 text-white-50">
                        <span class="spinner-border spinner-border-sm text-success me-1"></span> Loading FAQs...
                    </div>
                </div>

                <!-- Need More Help Card -->
                <div class="mt-3 p-3 rounded-3 border border-white-10 text-center" style="background: rgba(255,255,255,0.02);">
                    <div class="fs-5 text-warning mb-1"><i class="bi bi-headset"></i></div>
                    <h6 class="text-white fw-bold mb-1" style="font-size: 0.85rem;">Still need assistance?</h6>
                    <p class="text-white-50 mb-2" style="font-size: 0.74rem;">Our Canadian team is here to help you directly.</p>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-bold" id="btFaqToChatTabBtn" style="font-size: 0.78rem;">
                        <i class="bi bi-chat-dots-fill me-1"></i> Switch to Live Chat
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* ================= Bon Trouver Support Chat & FAQ Widget Styling ================= */
.bt-support-widget-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
}

/* Mini Teaser Banner */
.bt-support-teaser {
    position: absolute;
    bottom: 68px;
    right: 0;
    background: #0D243C;
    border: 1px solid rgba(73, 209, 125, 0.35);
    border-radius: 14px;
    padding: 8px 12px;
    width: 270px;
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    animation: btFadeInUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 9998;
}
.bt-teaser-avatar {
    position: relative;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(73, 209, 125, 0.15);
    border: 1px solid rgba(73, 209, 125, 0.4);
    color: #49D17D;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.bt-teaser-dot {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 8px;
    height: 8px;
    background: #10B981;
    border: 2px solid #0D243C;
    border-radius: 50%;
}
.bt-teaser-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
}
.bt-teaser-sub {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.65);
    line-height: 1.2;
}
.bt-teaser-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    font-size: 1.1rem;
    padding: 0;
    cursor: pointer;
    line-height: 1;
    transition: color 0.15s;
}
.bt-teaser-close:hover {
    color: #fff;
}

/* Floating Trigger Button */
.bt-support-trigger-btn {
    position: relative;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #49D17D 0%, #10B981 100%);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: #06182B;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    cursor: pointer;
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
    outline: none;
}
.bt-support-trigger-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 10px 25px rgba(73, 209, 125, 0.45) !important;
}
.bt-support-trigger-inner {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bt-support-badge {
    position: absolute;
    top: -12px;
    right: -12px;
    background: #EF4444;
    color: #fff;
    font-size: 0.68rem;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #06182B;
    padding: 0 4px;
}
.bt-support-pulse {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(73, 209, 125, 0.4);
    animation: btPulse 2.4s infinite;
    z-index: -1;
}
@keyframes btPulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    70% { transform: scale(1.4); opacity: 0; }
    100% { transform: scale(1.4); opacity: 0; }
}

/* Chat Window Shell */
.bt-support-window {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 32px);
    height: 560px;
    max-height: calc(100vh - 110px);
    background: #06182B;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 24px 50px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    animation: btSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes btSlideUp {
    from { opacity: 0; transform: translateY(18px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes btFadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Header */
.bt-support-header {
    background: linear-gradient(135deg, #0D243C 0%, #081D33 100%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    min-height: 54px;
}
.bt-support-avatar-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(73, 209, 125, 0.15);
    border: 1px solid rgba(73, 209, 125, 0.3);
    color: #49D17D;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
}
.bt-support-online-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 9px;
    height: 9px;
    background: #10B981;
    border: 2px solid #0D243C;
    border-radius: 50%;
}

/* Tabs Bar */
.bt-tabs-bar {
    background: #081D33;
}
.bt-tab-btn {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.78rem;
    font-weight: 600;
    padding: 6px 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bt-tab-btn:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
}
.bt-tab-btn.active {
    background: rgba(73, 209, 125, 0.15);
    border-color: rgba(73, 209, 125, 0.4);
    color: #49D17D;
}

/* Tab Content Containers */
.bt-tab-content {
    flex-grow: 1;
    overflow: hidden;
    height: calc(100% - 100px);
}
.bt-tab-content.active {
    display: flex !important;
}

/* Guest Chat Gate */
.bt-guest-chat-container {
    background: radial-gradient(circle at 50% 20%, rgba(73, 209, 125, 0.08) 0%, rgba(6, 24, 43, 0.95) 100%);
}
.bt-guest-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: rgba(73, 209, 125, 0.12);
    border: 1px solid rgba(73, 209, 125, 0.25);
    color: #49D17D;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

/* Messages container */
.bt-messages-container {
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    scroll-behavior: smooth;
    background: #06182B;
}
.bt-messages-container::-webkit-scrollbar,
.bt-faq-scroll::-webkit-scrollbar {
    width: 4px;
}
.bt-messages-container::-webkit-scrollbar-thumb,
.bt-faq-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 4px;
}

/* Message Bubbles */
.bt-message-item {
    display: flex;
    gap: 8px;
    max-width: 85%;
}
.bt-message-item.bt-msg-outgoing {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.bt-message-item.bt-msg-incoming {
    align-self: flex-start;
}
.bt-msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid rgba(255, 255, 255, 0.15);
    flex-shrink: 0;
}
.bt-msg-bubble {
    padding: 8px 12px;
    border-radius: 14px;
    font-size: 0.82rem;
    line-height: 1.45;
    word-break: break-word;
}
.bt-msg-outgoing .bt-msg-bubble {
    background: linear-gradient(135deg, #49D17D 0%, #10B981 100%);
    color: #06182B;
    font-weight: 500;
    border-bottom-right-radius: 3px;
}
.bt-msg-incoming .bt-msg-bubble {
    background: #0D243C;
    color: #F1F5F9;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-bottom-left-radius: 3px;
}
.bt-msg-system {
    align-self: center;
    background: rgba(255, 255, 255, 0.04);
    border: 1px dashed rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 0.74rem;
    color: rgba(255, 255, 255, 0.65);
    text-align: center;
    max-width: 90%;
}
.bt-msg-time {
    font-size: 0.65rem;
    opacity: 0.65;
    margin-top: 3px;
    display: block;
}
.bt-msg-outgoing .bt-msg-time {
    text-align: right;
    color: rgba(255, 255, 255, 0.65);
}

/* Typing Bubble Indicator */
.bt-typing-bubble {
    background: #0D243C;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.bt-dot {
    width: 5px;
    height: 5px;
    background: #49D17D;
    border-radius: 50%;
    animation: btBlink 1.4s infinite both;
}
.bt-dot:nth-child(2) { animation-delay: 0.2s; }
.bt-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes btBlink {
    0% { opacity: 0.2; transform: scale(0.8); }
    50% { opacity: 1; transform: scale(1.2); }
    100% { opacity: 0.2; transform: scale(0.8); }
}
.bi-spin {
    display: inline-block;
    animation: btIconSpin 1s infinite linear;
}
@keyframes btIconSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Chat Input Area */
.bt-chat-input-area {
    background: #081D33;
}
.bt-chat-textarea {
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
    border-radius: 18px !important;
    padding: 8px 12px !important;
    font-size: 0.82rem !important;
    resize: none !important;
    max-height: 80px;
}
.bt-chat-textarea:focus {
    background: #0D243C !important;
    border-color: #49D17D !important;
    box-shadow: 0 0 0 2px rgba(73, 209, 125, 0.2) !important;
}

/* Attachment Pre-send Previews */
.bt-attach-preview-item {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    padding: 4px 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
}
.bt-attach-preview-thumb {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    object-fit: cover;
}

/* Attached Media & Cards in Message Stream */
.bt-attached-image-wrapper {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    display: inline-block;
    border: 1px solid rgba(255, 255, 255, 0.15);
    cursor: pointer;
    background: #081D33;
    max-width: 220px;
}
.bt-attached-image-wrapper:hover .bt-attach-overlay {
    opacity: 1;
}
.bt-attached-img {
    display: block;
    max-width: 220px;
    max-height: 160px;
    width: auto;
    height: auto;
    object-fit: cover;
    transition: transform 0.2s ease;
}
.bt-attached-image-wrapper:hover .bt-attached-img {
    transform: scale(1.03);
}
.bt-attach-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.45);
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity 0.2s ease;
}
.bt-attach-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    color: #06182B;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: transform 0.15s ease;
}
.bt-attach-btn:hover {
    transform: scale(1.15);
}

.bt-attached-file-card {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 10px;
    backdrop-filter: blur(4px);
    transition: background 0.15s ease;
}
.bt-attached-file-card:hover {
    background: rgba(255, 255, 255, 0.12);
}

/* Lightbox Modal */
.bt-lightbox-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(4, 15, 27, 0.88);
    backdrop-filter: blur(8px);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: btFadeInUp 0.2s ease;
}
.bt-lightbox-content {
    background: #0D243C;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    max-width: 90vw;
    max-height: 90vh;
    box-shadow: 0 25px 60px rgba(0,0,0,0.8);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* FAQ Screen Styling */
.bt-faq-search-input {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
    border-radius: 12px !important;
    padding-left: 36px !important;
    padding-right: 32px !important;
    font-size: 0.8rem !important;
    height: 38px !important;
}
.bt-faq-search-input::placeholder {
    color: rgba(255, 255, 255, 0.45) !important;
}
.bt-faq-search-input:focus {
    background: #0D243C !important;
    border-color: #49D17D !important;
    box-shadow: 0 0 0 3px rgba(73, 209, 125, 0.18) !important;
}

.bt-topic-chip {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.72rem;
    font-weight: 500;
    padding: 3px 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}
.bt-topic-chip:hover,
.bt-topic-chip.active {
    background: rgba(73, 209, 125, 0.15);
    border-color: rgba(73, 209, 125, 0.4);
    color: #49D17D;
}

/* FAQ Items & Accordions */
.bt-faq-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 10px;
    margin-bottom: 6px;
    overflow: hidden;
    transition: background 0.2s;
}
.bt-faq-item:hover {
    background: rgba(255, 255, 255, 0.06);
}
.bt-faq-header {
    padding: 9px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8rem;
    font-weight: 600;
    color: #F1F5F9;
}
.bt-faq-body {
    display: none;
    padding: 8px 12px 10px 12px;
    font-size: 0.76rem;
    line-height: 1.45;
    color: rgba(255, 255, 255, 0.75);
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    white-space: pre-line;
}
.bt-faq-item.active .bt-faq-body {
    display: block;
}
.bt-faq-item.active .bt-faq-icon {
    transform: rotate(180deg);
}

@media (max-width: 480px) {
    .bt-support-widget-container {
        bottom: 16px;
        right: 16px;
    }
    .bt-support-window {
        bottom: 65px;
        right: -8px;
        width: calc(100vw - 20px);
        height: 520px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.getElementById('btSupportTrigger');
    const win = document.getElementById('btSupportWindow');
    const closeBtn = document.getElementById('btSupportCloseBtn');
    const minimizeBtn = document.getElementById('btSupportMinimizeBtn');
    const chatIcon = trigger.querySelector('.bt-support-icon-chat');
    const closeIcon = trigger.querySelector('.bt-support-icon-close');
    const unreadBadge = document.getElementById('btSupportUnreadBadge');
    
    const teaser = document.getElementById('btSupportTeaser');
    const closeTeaserBtn = document.getElementById('btCloseTeaserBtn');

    // Tabs
    const tabBtnChat = document.getElementById('btTabBtnChat');
    const tabBtnFaq = document.getElementById('btTabBtnFaq');
    const tabContentChat = document.getElementById('btTabContentChat');
    const tabContentFaq = document.getElementById('btTabContentFaq');
    const guestSwitchToFaqBtn = document.getElementById('btGuestSwitchToFaqBtn');
    const faqToChatTabBtn = document.getElementById('btFaqToChatTabBtn');

    let isWidgetOpen = false;
    let isAuthenticated = {{ $isGuest ? 'false' : 'true' }};
    let currentConversationId = null;
    let lastMessageId = 0;
    let pollInterval = null;
    let cachedFaqs = [];
    let activeCategory = 'all';

    // Show Teaser Banner after 2.5s (if not dismissed in session)
    if (!sessionStorage.getItem('bt_support_teaser_dismissed')) {
        setTimeout(() => {
            if (!isWidgetOpen && teaser) {
                teaser.classList.remove('d-none');
            }
        }, 2500);
    }

    if (closeTeaserBtn) {
        closeTeaserBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            teaser.classList.add('d-none');
            sessionStorage.setItem('bt_support_teaser_dismissed', 'true');
        });
    }

    if (teaser) {
        teaser.addEventListener('click', () => {
            teaser.classList.add('d-none');
            sessionStorage.setItem('bt_support_teaser_dismissed', 'true');
            toggleWidget(true);
        });
    }

    // Toggle Widget Open / Close
    function toggleWidget(forceState = null) {
        isWidgetOpen = forceState !== null ? forceState : !isWidgetOpen;
        if (isWidgetOpen) {
            win.classList.remove('d-none');
            chatIcon.classList.add('d-none');
            closeIcon.classList.remove('d-none');
            if (teaser) teaser.classList.add('d-none');
            sessionStorage.setItem('bt_support_teaser_dismissed', 'true');
            initWidget();
        } else {
            win.classList.add('d-none');
            chatIcon.classList.remove('d-none');
            closeIcon.classList.add('d-none');
            stopPolling();
        }
    }

    trigger.addEventListener('click', () => toggleWidget());
    if (closeBtn) closeBtn.addEventListener('click', () => toggleWidget(false));
    if (minimizeBtn) minimizeBtn.addEventListener('click', () => toggleWidget(false));

    // Tab Switching Logic
    function switchTab(tab) {
        if (tab === 'chat') {
            tabBtnChat.classList.add('active');
            tabBtnFaq.classList.remove('active');
            tabContentChat.classList.add('active');
            tabContentChat.classList.remove('d-none');
            tabContentFaq.classList.add('d-none');
            tabContentFaq.classList.remove('active');
            scrollToBottom();
        } else {
            tabBtnFaq.classList.add('active');
            tabBtnChat.classList.remove('active');
            tabContentFaq.classList.add('active');
            tabContentFaq.classList.remove('d-none');
            tabContentChat.classList.add('d-none');
            tabContentChat.classList.remove('active');
        }
    }

    if (tabBtnChat) tabBtnChat.addEventListener('click', () => switchTab('chat'));
    if (tabBtnFaq) tabBtnFaq.addEventListener('click', () => switchTab('faq'));
    if (guestSwitchToFaqBtn) guestSwitchToFaqBtn.addEventListener('click', () => switchTab('faq'));
    if (faqToChatTabBtn) faqToChatTabBtn.addEventListener('click', () => switchTab('chat'));

    // Initialize Widget Data
    function initWidget() {
        fetch('{{ route("support-chat.init") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            cachedFaqs = data.faqs || [];
            renderFaqs(cachedFaqs);

            if (data.authenticated) {
                isAuthenticated = true;
                currentConversationId = data.conversation.id;

                // Render initial messages
                renderMessages(data.messages || []);
                
                // Update Unread Count
                updateUnreadBadge(data.unread_count || 0);

                // Setup WebSocket connection
                setupSupportEcho();

                // Start fallback polling
                startPolling();
            } else {
                isAuthenticated = false;
            }
        })
        .catch(err => {
            console.error('Support widget init error:', err);
        });
    }

    // Render FAQs in FAQ tab
    function renderFaqs(faqs, query = '') {
        const faqAccordionList = document.getElementById('btFaqAccordionList');
        if (!faqAccordionList) return;

        let filtered = faqs;

        // Filter by category
        if (activeCategory !== 'all') {
            filtered = filtered.filter(f => f.id === activeCategory || (f.category && f.category.toLowerCase().includes(activeCategory)));
        }

        // Filter by text search
        if (query.trim() !== '') {
            const q = query.toLowerCase();
            filtered = filtered.filter(f => 
                f.title.toLowerCase().includes(q) || 
                f.content.toLowerCase().includes(q) ||
                (f.summary && f.summary.toLowerCase().includes(q))
            );
        }

        if (filtered.length === 0) {
            faqAccordionList.innerHTML = `<div class="text-center py-4 text-white-50" style="font-size: 0.8rem;">No matching articles found. Try another search or switch to Live Chat!</div>`;
            return;
        }

        const html = filtered.map(faq => `
            <div class="bt-faq-item" data-topic-id="${faq.id}">
                <div class="bt-faq-header" onclick="this.parentElement.classList.toggle('active')">
                    <span class="d-flex align-items-center gap-2 pe-2 text-truncate">
                        <i class="bi ${faq.icon || 'bi-question-circle'}" style="color: ${faq.color || '#49D17D'}; font-size: 0.95rem; flex-shrink: 0;"></i>
                        <span class="text-truncate">${escapeHtml(faq.title)}</span>
                    </span>
                    <i class="bi bi-chevron-down bt-faq-icon text-white-50" style="transition: transform 0.2s; font-size: 0.75rem; flex-shrink: 0;"></i>
                </div>
                <div class="bt-faq-body">
                    ${escapeHtml(faq.content).replace(/\n/g, '<br>')}
                    <div class="mt-2 pt-2 border-top border-white-05 d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-link text-success p-0 text-decoration-none bt-faq-ask-btn" data-topic-title="${escapeHtml(faq.title)}" style="font-size: 0.72rem;">
                            Ask support about this &rarr;
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        faqAccordionList.innerHTML = html;

        // Bind "Ask support about this" links
        document.querySelectorAll('.bt-faq-ask-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const title = this.getAttribute('data-topic-title');
                switchTab('chat');
                const chatInput = document.getElementById('btChatInput');
                if (chatInput) {
                    chatInput.value = `I have a question about: ${title}`;
                    chatInput.focus();
                }
            });
        });
    }

    // Render Messages list
    function renderMessages(messages, append = false) {
        const container = document.getElementById('btMessagesContainer');
        if (!container) return;

        const loading = document.getElementById('btMessagesLoading');
        if (loading) loading.remove();

        if (!append) {
            container.innerHTML = '';
        }

        messages.forEach(msg => {
            if (msg.id > lastMessageId) {
                lastMessageId = msg.id;
            }

            if (msg.sender_type === 'system') {
                const sysEl = document.createElement('div');
                sysEl.className = 'bt-msg-system';
                sysEl.innerHTML = `<i class="bi bi-info-circle me-1"></i> ${escapeHtml(msg.message)}`;
                container.appendChild(sysEl);
            } else {
                const msgEl = document.createElement('div');
                msgEl.className = `bt-message-item ${msg.is_sender ? 'bt-msg-outgoing' : 'bt-msg-incoming'}`;
                
                let attachmentsHtml = '';
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

                if (files && files.length > 0) {
                    attachmentsHtml = '<div class="mt-2 d-flex flex-column gap-2">';
                    files.forEach(f => {
                        const rawUrl = f.url || (typeof f === 'string' ? f : '');
                        const safeUrl = escapeHtml(resolveSafeUrl(rawUrl));
                        const safeName = escapeHtml(f.name || rawUrl.split('/').pop() || 'Attachment');
                        const sizeStr = f.size_human ? `<span class="opacity-75">(${f.size_human})</span>` : '';

                        if (f.is_image) {
                            attachmentsHtml += `
                                <div class="position-relative d-inline-block rounded-3 overflow-hidden" style="border: 1px solid rgba(255,255,255,0.12); background: #081D33;">
                                    <a href="javascript:void(0)" onclick="openBtLightbox('${safeUrl}', '${safeName}')">
                                        <img src="${safeUrl}" alt="${safeName}" class="img-fluid rounded-3" style="max-height: 160px; max-width: 220px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                    </a>
                                    <a href="${safeUrl}" download="${safeName}" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-1 rounded-circle border border-secondary border-opacity-25 shadow-sm" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;" title="Download">
                                        <i class="bi bi-download" style="font-size: 0.7rem;"></i>
                                    </a>
                                </div>
                            `;
                        } else {
                            attachmentsHtml += `
                                <div class="p-2 bg-light bg-opacity-10 rounded-3 d-flex align-items-center gap-2 border border-white-10">
                                    <i class="bi bi-file-earmark-fill fs-4 text-success"></i>
                                    <div class="min-w-0 me-2">
                                        <div class="text-white small fw-semibold text-truncate" style="font-size: 0.8rem; max-width: 180px;" title="${safeName}">${safeName}</div>
                                    </div>
                                    <a href="${safeUrl}" download="${safeName}" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size: 0.75rem;">Download</a>
                                </div>
                            `;
                        }
                    });
                    attachmentsHtml += '</div>';
                }

                const safeAvatar = msg.sender_avatar && msg.sender_avatar !== 'null' ? msg.sender_avatar : '{{ asset('images/default-avatar.svg') }}';
                msgEl.innerHTML = `
                    <img src="${safeAvatar}" alt="${escapeHtml(msg.sender_name || 'User')}" class="bt-msg-avatar" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';">
                    <div>
                        <div class="bt-msg-bubble shadow-sm">
                            ${msg.message ? escapeHtml(msg.message) : ''}
                            ${attachmentsHtml}
                        </div>
                        <span class="bt-msg-time">${msg.created_at_time || ''} ${msg.is_sender ? '<i class="bi bi-check2-all text-success ms-1"></i>' : ''}</span>
                    </div>
                `;
                container.appendChild(msgEl);
            }
        });

        scrollToBottom();
    }

    function scrollToBottom() {
        const container = document.getElementById('btMessagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function updateUnreadBadge(count) {
        if (!unreadBadge) return;
        if (count > 0) {
            unreadBadge.innerText = count > 99 ? '99+' : count;
            unreadBadge.classList.remove('d-none');
        } else {
            unreadBadge.classList.add('d-none');
        }
    }

    // Setup Real-time WebSockets via Laravel Reverb (Echo)
    function setupSupportEcho() {
        if (!window.Echo || !currentConversationId) return;

        // 1. Subscribe to the active support conversation channel
        window.Echo.private('support.conversation.' + currentConversationId)
            .listen('SupportMessageSent', (e) => {
                const currentUserId = {{ auth()->id() ?? 0 }};
                if (e.sender_id !== currentUserId) {
                    if (e.id > lastMessageId) {
                        e.is_sender = false;
                        if (isWidgetOpen) {
                            renderMessages([e], true);
                            scrollToBottom();
                        } else {
                            // Increment unread badge if widget is closed
                            const currentUnread = parseInt(unreadBadge?.innerText || '0', 10) || 0;
                            updateUnreadBadge(currentUnread + 1);
                        }
                    }
                }
            });
    }

    @if(auth()->check())
    // Subscribe to User channel on page load to catch incoming support notifications instantly
    if (window.Echo) {
        window.Echo.private('App.Models.User.{{ auth()->id() }}')
            .listen('SupportMessageSent', (e) => {
                if (e.sender_id !== {{ auth()->id() }}) {
                    if (!isWidgetOpen) {
                        const currentUnread = parseInt(unreadBadge?.innerText || '0', 10) || 0;
                        updateUnreadBadge(currentUnread + 1);
                        if (teaser) {
                            teaser.classList.remove('d-none');
                            const sub = teaser.querySelector('.bt-teaser-sub');
                            if (sub) sub.innerText = 'New reply from support team!';
                        }
                    }
                }
            });
    }
    @endif

    // Fallback polling for new messages (sync check every 15 seconds)
    function startPolling() {
        stopPolling();
        pollInterval = setInterval(() => {
            if (!isWidgetOpen || !currentConversationId) return;

            fetch(`/support-chat/messages/${currentConversationId}?after_id=${lastMessageId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.messages && data.messages.length > 0) {
                    renderMessages(data.messages, true);
                }
                if (data.conversation_status) {
                    const badge = document.getElementById('btConvStatusBadge');
                    if (badge) {
                        badge.innerHTML = `<i class="bi bi-circle-fill text-success" style="font-size: 5px;"></i> ${data.conversation_status.toUpperCase()}`;
                    }
                }
            })
            .catch(e => console.log('Poll sync idle'));
        }, 15000);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    // Attachment State Management
    let selectedFiles = [];

    const messageForm = document.getElementById('btSupportMessageForm');
    const chatInput = document.getElementById('btChatInput');
    const fileInput = document.getElementById('btChatFileInput');
    const attachBtn = document.getElementById('btAttachFileBtn');
    const attachmentPreview = document.getElementById('btAttachmentPreview');
    const attachmentPreviewList = document.getElementById('btAttachmentPreviewList');
    const typingIndicator = document.getElementById('btTypingIndicator');

    if (attachBtn && fileInput) {
        attachBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach(f => {
                    // Prevent duplicate by name + size
                    if (!selectedFiles.some(item => item.name === f.name && item.size === f.size)) {
                        if (selectedFiles.length < 5) {
                            selectedFiles.push(f);
                        }
                    }
                });
                renderAttachmentPreviews();
            }
            this.value = '';
        });
    }

    function renderAttachmentPreviews() {
        if (!attachmentPreview || !attachmentPreviewList) return;
        if (selectedFiles.length === 0) {
            attachmentPreview.classList.add('d-none');
            attachmentPreviewList.innerHTML = '';
            return;
        }

        attachmentPreview.classList.remove('d-none');
        attachmentPreviewList.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const itemEl = document.createElement('div');
            itemEl.className = 'bt-attach-preview-item text-white';

            const isImg = file.type.startsWith('image/');
            const sizeKb = (file.size / 1024).toFixed(0);

            if (isImg) {
                const reader = new FileReader();
                reader.onload = e => {
                    itemEl.innerHTML = `
                        <img src="${e.target.result}" class="bt-attach-preview-thumb me-1">
                        <span class="small text-truncate" style="max-width: 110px; font-size: 0.72rem;">${escapeHtml(file.name)}</span>
                        <span class="text-white-50" style="font-size: 0.65rem;">${sizeKb}KB</span>
                        <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.55rem;" onclick="removeSelectedFile(${index})"></button>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                let icon = 'bi-file-earmark-text';
                if (file.type.includes('pdf') || file.name.endsWith('.pdf')) icon = 'bi-file-earmark-pdf text-danger';
                else if (file.name.match(/\.(doc|docx)$/i)) icon = 'bi-file-earmark-word text-primary';
                else if (file.name.match(/\.(xls|xlsx|csv)$/i)) icon = 'bi-file-earmark-excel text-success';

                itemEl.innerHTML = `
                    <i class="bi ${icon} fs-6 me-1"></i>
                    <span class="small text-truncate" style="max-width: 110px; font-size: 0.72rem;">${escapeHtml(file.name)}</span>
                    <span class="text-white-50" style="font-size: 0.65rem;">${sizeKb}KB</span>
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.55rem;" onclick="removeSelectedFile(${index})"></button>
                `;
            }

            attachmentPreviewList.appendChild(itemEl);
        });
    }

    window.removeSelectedFile = function(index) {
        selectedFiles.splice(index, 1);
        renderAttachmentPreviews();
    };

    if (chatInput) {
        chatInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    if (messageForm) {
        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = chatInput.value.trim();
            const hasFiles = selectedFiles.length > 0;

            if (!text && !hasFiles) return;
            if (!currentConversationId) return;

            const sendBtn = document.getElementById('btSendMessageBtn');
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            if (typingIndicator) typingIndicator.classList.remove('d-none');

            const formData = new FormData();
            formData.append('message', text);
            formData.append('_token', '{{ csrf_token() }}');
            
            selectedFiles.forEach(file => {
                formData.append('attachments[]', file);
            });

            fetch(`/support-chat/messages/${currentConversationId}/send`, {
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
                sendBtn.innerHTML = '<i class="bi bi-send-fill" style="font-size: 0.85rem; margin-left: 2px;"></i>';
                if (typingIndicator) typingIndicator.classList.add('d-none');

                if (data.status === 'success' && data.message) {
                    chatInput.value = '';
                    selectedFiles = [];
                    renderAttachmentPreviews();
                    renderMessages([data.message], true);
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(err => {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="bi bi-send-fill" style="font-size: 0.85rem; margin-left: 2px;"></i>';
                if (typingIndicator) typingIndicator.classList.add('d-none');
                console.error('Send failed:', err);
            });
        });
    }



    // Global Lightbox Helpers
    window.openBtLightbox = function(url, title = 'Image View') {
        const overlay = document.getElementById('btLightboxOverlay');
        const img = document.getElementById('btLightboxImg');
        const titleEl = document.getElementById('btLightboxTitle');
        const dlBtn = document.getElementById('btLightboxDownloadBtn');

        if (overlay && img) {
            img.src = url;
            if (titleEl) titleEl.innerText = title;
            if (dlBtn) {
                dlBtn.href = url;
                dlBtn.setAttribute('download', title);
            }
            overlay.classList.remove('d-none');
        }
    };

    window.closeBtLightbox = function() {
        const overlay = document.getElementById('btLightboxOverlay');
        if (overlay) {
            overlay.classList.add('d-none');
        }
    };

    function resolveSafeUrl(url) {
        if (!url) return '';
        return url.replace(/^https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?(\/.*)$/i, '$1');
    }

    function escapeHtml(string) {
        if (!string) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return string.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>
