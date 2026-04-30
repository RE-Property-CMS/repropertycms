<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LicenseDomain;
use App\Models\LicenseKey;
use App\Models\LicenseVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseVerifyController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $keyString = trim($request->input('key', ''));
        $domain    = trim($request->input('domain', ''));
        $ip        = $request->ip();

        $licenseKey = LicenseKey::where('key', $keyString)->first();

        if (!$licenseKey) {
            $this->log(null, $domain, $ip, 'invalid_key');
            return response()->json(['valid' => false, 'message' => 'Invalid license key.']);
        }

        if ($licenseKey->status === 'revoked') {
            $this->log($licenseKey->id, $domain, $ip, 'revoked');
            return response()->json(['valid' => false, 'message' => 'This license key has been revoked. Please contact support.']);
        }

        if ($licenseKey->isExpired()) {
            $this->log($licenseKey->id, $domain, $ip, 'expired');
            return response()->json(['valid' => false, 'message' => 'This license key has expired. Please contact support.']);
        }

        // Domain tracking and limit enforcement
        $existingDomain = LicenseDomain::where('license_key_id', $licenseKey->id)
            ->where('domain', $domain)
            ->first();

        if ($existingDomain) {
            $existingDomain->update([
                'last_seen'          => now(),
                'verification_count' => $existingDomain->verification_count + 1,
            ]);
        } else {
            $usedCount = LicenseDomain::where('license_key_id', $licenseKey->id)->count();

            if ($usedCount >= $licenseKey->max_domains) {
                $this->log($licenseKey->id, $domain, $ip, 'domain_limit_reached');
                return response()->json([
                    'valid'   => false,
                    'message' => "Domain limit reached. This license permits up to {$licenseKey->max_domains} domains. Contact support to upgrade.",
                ]);
            }

            LicenseDomain::create([
                'license_key_id'     => $licenseKey->id,
                'domain'             => $domain,
                'first_seen'         => now(),
                'last_seen'          => now(),
                'verification_count' => 1,
            ]);
        }

        $this->log($licenseKey->id, $domain, $ip, 'success');

        return response()->json([
            'valid' => true,
            'token' => bcrypt('repropertycms'),
        ]);
    }

    private function log(?int $keyId, string $domain, string $ip, string $result): void
    {
        LicenseVerification::create([
            'license_key_id' => $keyId,
            'domain'         => $domain,
            'ip'             => $ip,
            'result'         => $result,
            'verified_at'    => now(),
        ]);
    }
}
