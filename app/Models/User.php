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
        'avatar',
        'bio',
        'community_points',
        'is_verified'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_dealer' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator' || $this->role === 'admin';
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

    // Computed attributes (Accessors)
    public function getRatingAttribute()
    {
        return $this->reviewsReceived()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviewsReceived()->count();
    }
}
