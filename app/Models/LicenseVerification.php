<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseVerification extends Model
{
    public $timestamps = false;

    protected $fillable = ['license_key_id', 'domain', 'ip', 'result', 'verified_at'];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function licenseKey(): BelongsTo
    {
        return $this->belongsTo(LicenseKey::class);
    }
}
