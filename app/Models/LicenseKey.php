<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LicenseKey extends Model
{
    protected $fillable = ['license_buyer_id', 'key', 'status', 'max_domains', 'notes', 'expires_at'];

    protected $casts = [
        'expires_at'  => 'datetime',
        'max_domains' => 'integer',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(LicenseBuyer::class, 'license_buyer_id');
    }

    public function licenseDomains(): HasMany
    {
        return $this->hasMany(LicenseDomain::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(LicenseVerification::class);
    }

    public function domainsUsed(): int
    {
        return $this->licenseDomains()->count();
    }

    public function isAtLimit(): bool
    {
        return $this->domainsUsed() >= $this->max_domains;
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->expires_at !== null && $this->expires_at->isPast());
    }
}
