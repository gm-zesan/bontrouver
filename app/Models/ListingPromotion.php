<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'promotion_package_id',
        'type',
        'price_paid',
        'points_spent',
        'payment_method',
        'payment_status',
        'transaction_reference',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'points_spent' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(PromotionPackage::class, 'promotion_package_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }
}
