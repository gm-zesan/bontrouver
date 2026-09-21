<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_dealer',
        'phone',
        'city',
        'province',
        'postal_code',
        'location',
        'avatar',
        'bio',
        'community_points',
        'is_verified',
        'is_suspended',
        'notification_preferences',
        'admin_notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'is_verified' => false,
        'is_dealer' => false,
        'role' => UserRole::USER,
        'community_points' => 0,
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_dealer' => 'boolean',
        'is_verified' => 'boolean',
        'is_suspended' => 'boolean',
        'community_points' => 'integer',
        'notification_preferences' => 'array',
        'role' => UserRole::class,
    ];

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function wantsNotification(string $type): bool
    {
        $prefs = $this->notification_preferences;
        if (empty($prefs) || !is_array($prefs)) {
            return true;
        }

        return (bool) ($prefs[$type] ?? true);
    }

    // Relationships
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function purchases()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    public function sales()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function smartAlerts()
    {
        return $this->hasMany(SmartAlert::class);
    }

    public function companionshipRequests()
    {
        return $this->hasMany(CompanionshipRequest::class);
    }

    public function companionshipAttendees()
    {
        return $this->hasMany(CompanionshipAttendee::class);
    }

    public function verifications()
    {
        return $this->hasMany(UserVerification::class);
    }

    public function latestVerification()
    {
        return $this->hasOne(UserVerification::class)->latestOfMany();
    }

    public function getVerificationStatusAttribute(): string
    {
        if ($this->is_verified) {
            return 'approved';
        }
        $latest = $this->latestVerification;
        return $latest ? $latest->status : 'none';
    }

    // Computed attributes (Accessors)
    public function getRatingAttribute(): float
    {
        return round((float) ($this->reviewsReceived()->avg('rating') ?? 0), 2);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviewsReceived()->count();
    }

    public function getCompletedTransactionsCountAttribute(): int
    {
        return $this->sales()->where('status', 'completed')->count() 
             + $this->purchases()->where('status', 'completed')->count();
    }

    public function getMemberTierAttribute(): array
    {
        $pts = (int) ($this->community_points ?? 0);

        try {
            $tiersData = cache()->remember('member_tiers_all_v2', 3600, function () {
                return MemberTier::orderBy('min_points', 'asc')->get()->toArray();
            });
            $tiers = collect($tiersData);
        } catch (\Throwable $e) {
            $tiers = collect();
        }

        if ($tiers->isEmpty()) {
            return [
                'name' => 'Member',
                'full_name' => 'Member',
                'short_name' => 'Member',
                'icon' => '👤',
                'level' => 1,
                'badge_class' => 'tier-badge tier-bronze',
                'min' => 0,
                'max' => null,
                'next_threshold' => null,
                'next_tier' => null,
                'points_needed' => 0,
                'progress_percentage' => 100,
                'model' => null,
            ];
        }

        $currentTier = null;
        $currentLevel = 1;
        $nextTier = null;
        $totalTiers = $tiers->count();

        foreach ($tiers as $index => $tier) {
            $level = $index + 1;
            $min = (int) (is_array($tier) ? $tier['min_points'] : $tier->min_points);
            $maxRaw = is_array($tier) ? ($tier['max_points'] ?? null) : $tier->max_points;
            $max = $maxRaw !== null ? (int) $maxRaw : null;

            if ($pts >= $min && ($max === null || $pts <= $max)) {
                $currentTier = $tier;
                $currentLevel = $level;
                $nextTier = $tiers->get($index + 1);
                break;
            }
        }

        if (!$currentTier) {
            $currentTier = $tiers->last();
            $currentLevel = $totalTiers;
            $nextTier = null;
        }

        $tierName = is_array($currentTier) ? $currentTier['name'] : $currentTier->name;
        $icon = '🥉';
        if (preg_match('/^([\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F1E6}-\x{1F1FF}⭐🥇🥈🥉])\s*(.*)$/u', $tierName, $matches)) {
            $icon = $matches[1];
            $cleanName = trim($matches[2]);
        } else {
            $cleanName = $tierName;
        }

        $badgeClass = match ($currentLevel) {
            4 => 'tier-badge tier-platinum',
            3 => 'tier-badge tier-gold',
            2 => 'tier-badge tier-silver',
            default => 'tier-badge tier-bronze',
        };
        if ($currentLevel >= 4) {
            $badgeClass = 'tier-badge tier-platinum';
        }

        $pointsNeeded = 0;
        $progressPercentage = 100;
        $nextTierName = null;
        $nextThreshold = null;

        $currentMin = (int) (is_array($currentTier) ? $currentTier['min_points'] : $currentTier->min_points);
        $currentMax = (is_array($currentTier) ? ($currentTier['max_points'] ?? null) : $currentTier->max_points);

        if ($nextTier) {
            $nextThreshold = (int) (is_array($nextTier) ? $nextTier['min_points'] : $nextTier->min_points);
            $pointsNeeded = max(0, $nextThreshold - $pts);
            $tierRange = $nextThreshold - $currentMin;
            if ($tierRange > 0) {
                $progressPercentage = min(100, max(0, round((($pts - $currentMin) / $tierRange) * 100)));
            } else {
                $progressPercentage = 0;
            }
            $nextRawName = is_array($nextTier) ? $nextTier['name'] : $nextTier->name;
            $nextTierName = preg_replace('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F1E6}-\x{1F1FF}⭐🥇🥈🥉\s]+/u', '', $nextRawName);
        }

        return [
            'name' => $cleanName,
            'full_name' => $tierName,
            'short_name' => str_replace(' Member', '', $cleanName),
            'icon' => $icon,
            'level' => $currentLevel,
            'badge_class' => $badgeClass,
            'min' => $currentMin,
            'max' => $currentMax !== null ? (int) $currentMax : null,
            'next_threshold' => $nextThreshold,
            'next_tier' => $nextTierName,
            'points_needed' => $pointsNeeded,
            'progress_percentage' => $progressPercentage,
            'model' => $currentTier,
        ];
    }
}
