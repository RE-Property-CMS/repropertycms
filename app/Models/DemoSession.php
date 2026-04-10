<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoSession extends Model
{
    protected $table = 'demo_sessions';

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
