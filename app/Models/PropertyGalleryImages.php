<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyGalleryImages extends Model
{
    use HasFactory;

    protected $table = 'property_gallery_images';

    protected $primarykey = 'id';

    protected $guarded = [];

    protected $with = ['property_images'];

    public function property_images()
    {
        return $this->belongsTo(Property_images::class, 'property_image_id', 'id');
    }
}
