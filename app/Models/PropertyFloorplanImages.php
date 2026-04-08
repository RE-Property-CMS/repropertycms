<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFloorplanImages extends Model
{
    use HasFactory;

    protected $table = 'property_floorplan_images';

    protected $primarykey = 'id';

    public function property_images()
    {
        return $this->hasMany(PropertyImages::class, 'id', 'property_image_id');
    }
}
