<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LicenseBuyer extends Model
{
    protected $fillable = ['name', 'email', 'notes'];

    public function licenseKeys(): HasMany
    {
        return $this->hasMany(LicenseKey::class);
    }

    public function totalDomainsUsed(): int
    {
        return $this->licenseKeys()->withCount('licenseDomains')->get()
            ->sum('license_domains_count');
    }
}
