<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

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
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'is_verified' => false,
        'is_dealer' => false,
        'role' => 'user',
        'community_points' => 0,
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_dealer' => 'boolean',
        'is_verified' => 'boolean',
        'community_points' => 'integer',
        'notification_preferences' => 'array',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator' || $this->role === 'admin';
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

        if ($pts >= 700) {
            return [
                'name' => 'Highly Appreciated Member',
                'short_name' => 'Highly Appreciated',
                'icon' => '⭐',
                'level' => 4,
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                'min' => 700,
                'max' => null,
                'next_threshold' => null,
                'next_tier' => null,
                'points_needed' => 0,
                'progress_percentage' => 100,
            ];
        }

        if ($pts >= 300) {
            $needed = 700 - $pts;
            $percent = min(100, max(0, round((($pts - 300) / 400) * 100)));
            return [
                'name' => 'Trusted Member',
                'short_name' => 'Trusted',
                'icon' => '🥇',
                'level' => 3,
                'badge_class' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'min' => 300,
                'max' => 699,
                'next_threshold' => 700,
                'next_tier' => 'Highly Appreciated Member',
                'points_needed' => $needed,
                'progress_percentage' => $percent,
            ];
        }

        if ($pts >= 100) {
            $needed = 300 - $pts;
            $percent = min(100, max(0, round((($pts - 100) / 200) * 100)));
            return [
                'name' => 'Active Member',
                'short_name' => 'Active',
                'icon' => '🥈',
                'level' => 2,
                'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
                'min' => 100,
                'max' => 299,
                'next_threshold' => 300,
                'next_tier' => 'Trusted Member',
                'points_needed' => $needed,
                'progress_percentage' => $percent,
            ];
        }

        $needed = 100 - $pts;
        $percent = min(100, max(0, round(($pts / 100) * 100)));
        return [
            'name' => 'New Member',
            'short_name' => 'New',
            'icon' => '🥉',
            'level' => 1,
            'badge_class' => 'bg-secondary-subtle text-light border border-secondary border-opacity-25',
            'min' => 0,
            'max' => 99,
            'next_threshold' => 100,
            'next_tier' => 'Active Member',
            'points_needed' => $needed,
            'progress_percentage' => $percent,
        ];
    }
}
