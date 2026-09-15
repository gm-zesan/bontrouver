<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartAlertAttribute extends Model
{
    protected $fillable = [
        'smart_alert_id',
        'category_attribute_id',
        'value',
    ];

    public function smartAlert()
    {
        return $this->belongsTo(SmartAlert::class);
    }

    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }
}
