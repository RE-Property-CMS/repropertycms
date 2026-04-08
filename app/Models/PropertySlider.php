<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySlider extends Model
{
    use HasFactory;

    protected $table = 'property_slider';

    protected $primarykey = 'id';

    public function property_images()
    {
        return $this->belongsTo(PropertyImages::class, 'image_id', 'id');
    }
}
