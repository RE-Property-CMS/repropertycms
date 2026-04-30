<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LicenseVerifier
{
    private const API_URL = 'https://app.realtyinterface.com/api/license/verify';

    // RSA-2048 public key — hardcoded so it cannot be swapped via .env.
    // Generate with: openssl genrsa -out private.pem 2048 && openssl rsa -in private.pem -pubout
    // Keep the private key only on app.realtyinterface.com — never commit it.
    private const PUBLIC_KEY = <<<'PEM'
-----BEGIN PUBLIC KEY-----
REPLACE_WITH_REAL_PUBLIC_KEY_AFTER_GENERATION
-----END PUBLIC KEY-----
PEM;

    /**
     * Call the licensing server. Returns the raw response body.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public static function call(string $key, string $domain, string $ip): array
    {
        return Http::timeout(12)->acceptJson()
            ->post(self::API_URL, compact('key', 'domain', 'ip'))
            ->json() ?? [];
    }

    /**
     * Verify the signed payload returned by the licensing server.
     *
     * All four checks must pass:
     *   1. RSA-SHA256 signature is valid against the hardcoded public key
     *   2. payload["domain"] matches the current installation domain exactly
     *   3. payload["ts"] timestamp is within the last 5 minutes (anti-replay)
     *   4. payload["valid"] is true
     */
    public static function verify(string $rawData, string $rawSig, string $currentDomain): bool
    {
        $result = openssl_verify($rawData, $rawSig, self::PUBLIC_KEY, OPENSSL_ALGO_SHA256);

        if ($result !== 1) {
            return false;
        }

        $payload = json_decode($rawData, true);

        return ($payload['valid'] ?? false) === true
            && ($payload['domain'] ?? '') === $currentDomain
            && abs(time() - ($payload['ts'] ?? 0)) <= 300;
    }
}
