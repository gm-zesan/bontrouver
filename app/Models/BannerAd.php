<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'position',
        'image_path',
        'target_url',
        'html_code',
        'city',
        'province',
        'is_active',
        'sort_order',
        'impressions_count',
        'clicks_count',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://') || str_starts_with($this->image_path, '//')) {
            return $this->image_path;
        }

        return \Illuminate\Support\Facades\Storage::url($this->image_path);
    }

    public function recordImpression(): void
    {
        $this->increment('impressions_count');
    }

    public function recordClick(): void
    {
        $this->increment('clicks_count');
    }
}
