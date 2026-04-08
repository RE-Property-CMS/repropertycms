<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandSettings extends Model
{
    protected $table = 'brand_settings';

    protected $fillable = [
        'primary_color',
        'secondary_color',
        'accent_color',
        'accent_2_color',
        'sidebar_color',
        'font_body',
        'font_heading',
        'font_admin',
        'logo_path',
        'favicon_path',
        'website_url',
        'instagram_url',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
    ];
}
