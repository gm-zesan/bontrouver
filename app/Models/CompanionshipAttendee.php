<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanionshipAttendee extends Model
{
    protected $fillable = [
        'companionship_request_id',
        'user_id',
        'status',
    ];

    public function companionshipRequest()
    {
        return $this->belongsTo(CompanionshipRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
