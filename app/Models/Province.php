<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'name',
        'code',
        'slug',
        'country_code',
        'sort_order',
    ];

    public function cities()
    {
        return $this->hasMany(City::class)->orderBy('sort_order');
    }

    public function listings()
    {
        return $this->hasManyThrough(Listing::class, City::class);
    }

    public function smartAlerts()
    {
        return $this->hasMany(SmartAlert::class);
    }
}
