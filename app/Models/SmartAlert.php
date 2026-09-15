<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartAlert extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'keyword',
        'category_id',
        'city_id',
        'province_id',
        'city',
        'min_price',
        'max_price',
        'is_active',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cityRelation()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function attributes()
    {
        return $this->hasMany(SmartAlertAttribute::class);
    }
}
