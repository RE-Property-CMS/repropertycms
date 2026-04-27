<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class EnvService
{
    /**
     * Update or create environment variables safely.
     *
     * Used by Setup Wizard to store database, mail,
     * payment and other infrastructure credentials.
     */
    public function set(array $data): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {

            $value = '"' . trim($value) . '"';

            if (str_contains($envContent, "{$key}=")) {
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                $envContent .= PHP_EOL . "{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);

        // Clear the config cache file directly to avoid Artisan context issues
        // when called from within a web request (Artisan::call can throw uncatchable errors)
        $configCache = base_path('bootstrap/cache/config.php');
        if (file_exists($configCache)) {
            @unlink($configCache);
        }

        if (app()->isProduction()) {
            try {
                Artisan::call('optimize');
            } catch (\Throwable) {
                // Best-effort: cache already cleared above
            }
        }
    }
}
