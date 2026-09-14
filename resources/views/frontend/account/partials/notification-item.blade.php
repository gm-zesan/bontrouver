@php
    $isUnread = empty($notif['read']);
@endphp

<div class="dark-surface-card p-3 p-md-3 rounded-3 d-flex align-items-center justify-content-between gap-3 notification-card {{ $isUnread ? 'notification-unread-card' : '' }}" 
     id="notif-item-{{ $notif['id'] }}"
     style="background: {{ $isUnread ? '#0D243C' : '#091B2E' }}; border: 1px solid {{ $isUnread ? 'rgba(73, 209, 125, 0.2)' : 'rgba(255, 255, 255, 0.05)' }}; transition: all 0.2s ease;">
    
    <div class="d-flex align-items-start gap-3 min-w-0">
        <!-- Notification Icon -->
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
             style="width: 42px; height: 42px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
            <i class="bi {{ $notif['icon'] }} fs-5"></i>
        </div>

        <!-- Notification Message Content -->
        <div class="min-w-0">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h6 class="fw-bold text-white mb-0" style="font-size: 0.92rem;">
                    {{ $notif['title'] }}
                </h6>
                @if($isUnread)
                    <span class="unread-dot badge bg-warning text-dark p-1 rounded-circle" style="width: 7px; height: 7px;" title="Unread notification"></span>
                @endif
                <span class="text-secondary small ms-auto d-md-none" style="font-size: 0.72rem;">{{ $notif['time'] }}</span>
            </div>

            <p class="text-secondary small mb-2 text-truncate-2" style="font-size: 0.84rem; line-height: 1.45;">
                {{ $notif['body'] }}
            </p>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ $notif['action_url'] }}" class="text-success small fw-semibold text-decoration-none hover-brand-green" style="font-size: 0.82rem;">
                    {{ $notif['action_label'] }} <i class="bi bi-arrow-right ms-1"></i>
                </a>
                <span class="text-secondary small d-none d-md-inline opacity-50" style="font-size: 0.75rem;">•</span>
                <span class="text-secondary small d-none d-md-inline" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>{{ $notif['time'] }}</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        @if($isUnread)
            <button type="button" 
                    class="btn btn-sm btn-dark border border-secondary border-opacity-25 text-secondary px-2 py-1" 
                    title="Mark as read"
                    onclick="markSingleRead({{ $notif['id'] }}, this)"
                    style="font-size: 0.75rem;">
                <i class="bi bi-check2"></i>
            </button>
        @endif
    </div>

</div>
