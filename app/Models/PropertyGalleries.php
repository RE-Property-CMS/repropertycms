<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyGalleries extends Model
{
    use HasFactory;

    protected $table = 'property_galleries';

    protected $primarykey = 'id';

    protected $guarded = [];

    public function property_gallery_images()
    {
        return $this->hasMany(PropertyGalleryImages::class, 'gallery_id', 'id');
    }

    public function property_images()
    {
        return $this->hasMany(Property_images::class, 'id', 'gallery_image_id');
    }
}
