<?php

namespace App\Notifications;

use App\Models\UserVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly UserVerification $verification,
        public readonly string $status,
        public readonly ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isApproved = $this->status === 'approved';

        return [
            'type' => 'verification_status',
            'status' => $this->status,
            'title' => $isApproved ? 'Identity Verification Approved! 🎉' : 'Verification Document Not Approved',
            'message' => $isApproved 
                ? 'Your Canadian identity verification has been approved! You now have the Verified Member badge and +50 Community Points.'
                : 'Your verification document was reviewed but could not be approved. Reason: ' . ($this->reason ?? 'Document unreadable or invalid.') . ' You can submit a new document anytime.',
            'action_url' => route('account.verification.index'),
            'verification_id' => $this->verification->id,
            'document_type' => $this->verification->document_type,
            'icon' => $isApproved ? 'bi-patch-check-fill text-success' : 'bi-shield-exclamation text-danger',
        ];
    }
}
