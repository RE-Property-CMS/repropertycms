<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAmenities extends Model
{
    use HasFactory;

    protected $table = 'property_amenities';

    protected $primarykey = 'id';

    protected $guarded = [];

    public function Properties()
    {
        return $this->belongsTo(Properties::class, 'property_id', 'id');
    }

    public function Amenities()
    {
        return $this->belongsTo(Amenities::class, 'amenity_id', 'id');
    }
}
