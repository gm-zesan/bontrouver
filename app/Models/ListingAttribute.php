<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingAttribute extends Model
{
    protected $fillable = [
        'listing_id',
        'category_attribute_id',
        'value',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }
}
