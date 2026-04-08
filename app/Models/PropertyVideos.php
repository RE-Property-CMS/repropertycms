<?php

namespace App\Models;

use App\Enums\VideoType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyVideos extends Model
{
    use HasFactory;

    protected $table = 'property_videos';

    protected $primarykey = 'id';

    protected $guarded = [];

    protected $casts = [
        'video_type' => VideoType::class,
    ];
}
