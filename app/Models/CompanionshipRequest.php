<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanionshipRequest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'meetup_date_time',
        'city_id',
        'location_name',
        'city',
        'province',
        'headcount_limit',
        'status',
    ];

    protected $casts = [
        'meetup_date_time' => 'datetime',
        'headcount_limit' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cityRelation()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function attendees()
    {
        return $this->hasMany(CompanionshipAttendee::class);
    }
}
