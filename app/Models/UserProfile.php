<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'cover_image_path',
        'about_text',
        'website_url',
        'social_links',
        'operating_hours',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'operating_hours' => 'array',
            'features' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}