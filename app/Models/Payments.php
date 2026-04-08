<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Agents;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $primarykey = 'id';

    public function agent()
    {
        return $this->belongsTo(Agents::class, 'agent_id');
    }
}
