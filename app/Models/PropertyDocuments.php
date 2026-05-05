<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDocuments extends Model
{
    use HasFactory;

    protected $table = 'property_documents';

    protected $primarykey = 'id';

    protected $guarded = [];
}
