<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_key',
        'name',
        'type',
        'points',
        'category',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'points'     => 'integer',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEarn(Builder $query): Builder
    {
        return $query->where('type', 'earn');
    }

    public function scopeSpend(Builder $query): Builder
    {
        return $query->where('type', 'spend');
    }
}
