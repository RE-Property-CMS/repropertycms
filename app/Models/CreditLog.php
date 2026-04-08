<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditLog extends Model
{
    use HasFactory;

    protected $table = 'credit_logs';

    protected $primarykey = 'id';

    public function agents()
    {
        return $this->hasOne(Agents::class, 'id', 'agent_id');
    }
}
