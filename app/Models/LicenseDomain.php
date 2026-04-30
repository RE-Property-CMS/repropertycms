<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseDomain extends Model
{
    public $timestamps = false;

    protected $fillable = ['license_key_id', 'domain', 'first_seen', 'last_seen', 'verification_count'];

    protected $casts = [
        'first_seen' => 'datetime',
        'last_seen'  => 'datetime',
    ];

    public function licenseKey(): BelongsTo
    {
        return $this->belongsTo(LicenseKey::class);
    }
}
