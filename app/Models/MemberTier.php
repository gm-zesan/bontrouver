<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberTier extends Model
{
    public const CACHE_KEY = 'member_tiers';

    protected $fillable = [
        'name',
        'icon',
        'badge_color',
        'badge_class',
        'min_points',
        'max_points',
        'description',
        'perks',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
        'perks'      => 'array',
    ];

    /**
     * Get clean formatted display name without leading emoji.
     */
    public function getCleanNameAttribute(): string
    {
        return trim(preg_replace('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F1E6}-\x{1F1FF}]\s*/u', '', $this->name));
    }
}
