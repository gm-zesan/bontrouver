<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberTier extends Model
{
    protected $fillable = [
        'name',
        'min_points',
        'max_points',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
    ];
}
