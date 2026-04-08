<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $guarded = [];

    public function floorplan()
    {
        return $this->belongsTo(PropertyFloorplans::class);
    }

    public function propertyImages()
    {
        return $this->belongsToMany(PropertyImages::class, 'hotspot_property_images');
    }
}
