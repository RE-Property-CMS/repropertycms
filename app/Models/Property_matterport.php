<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property_matterport extends Model
{
    use HasFactory;

    protected $table = 'property_matterport';

    protected $primarykey = 'id';

    protected $guarded = [];
}
