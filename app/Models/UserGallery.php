<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGallery extends Model
{
    protected $fillable = [
        'user_id',
        'image_path',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}