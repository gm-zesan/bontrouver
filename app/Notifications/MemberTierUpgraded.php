<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MemberTierUpgraded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly array $newTier,
        public readonly array $oldTier,
        public readonly int $points
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $tierName = $this->newTier['name'] ?? 'Active Member';
        $level = $this->newTier['level'] ?? 1;
        $icon = $this->newTier['icon'] ?? '🥉';

        return [
            'type' => 'member_tier_upgraded',
            'title' => "🎉 Level Up! You reached {$tierName}!",
            'message' => "Congratulations! With " . number_format($this->points) . " Community Points, you have unlocked {$tierName} (Level {$level}) and exclusive community privileges.",
            'tier_name' => $tierName,
            'tier_icon' => $icon,
            'tier_level' => $level,
            'action_url' => route('account.points'),
            'action_label' => 'View My Perks & Standing',
            'icon' => 'bi-trophy-fill text-warning',
        ];
    }
}
