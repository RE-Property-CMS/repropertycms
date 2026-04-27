<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAppSetup
{
    /**
     * Handle an incoming request.
     *
     * Controls access based on the application's installation status.
     *
     * Behaviour matrix:
     *
     * NOT Installed:
     *   /setup                 → Allow (key-entry landing)
     *   /setup/verify-key      → Allow
     *   /setup/test-*          → Allow (AJAX credential testing)
     *   /setup/database-repair → Allow
     *   /setup/*               → Require setup_verified session, else → /setup
     *   /admin/*               → Redirect → /setup
     *   Any other URL          → Show "not configured" page (503)
     *
     * IS Installed:
     *   /setup/database-repair → Allow
     *   /setup/*               → Block → /admin/dashboard
     *   All others             → Verify DB integrity, then pass through
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = Config::get('app.installed');

        // Classify request path
        $isSetupEntry     = $request->is('setup') || $request->is('setup/');
        $isSetupVerifyKey = $request->is('setup/verify-key');
        $isSetupTestRoute = $request->is('setup/test-*');
        $isSetupRepair    = $request->is('setup/database-repair');

        /*
        |--------------------------------------------------------------------------
        | Application Is NOT Installed
        |--------------------------------------------------------------------------
        */
        if (!$isInstalled) {

            // Always allow: landing, key verification, AJAX tests, repair
            if ($isSetupEntry || $isSetupVerifyKey || $isSetupTestRoute || $isSetupRepair) {
                return $next($request);
            }

            // Wizard steps require the setup key to have been verified first
            if ($request->is('setup*')) {
                if (!session('setup_verified')) {
                    return redirect()->route('setup.index')
                        ->with('info', 'Please enter your setup key to begin installation.');
                }
                return $next($request);
            }

            // Admin panel → redirect to setup wizard
            if ($request->is('admin*')) {
                return redirect()->route('setup.index');
            }

            // All public/agent/auth routes → show "not yet configured" page (503)
            return response()->view('setup.not-configured', [], 503);
        }

        /*
        |--------------------------------------------------------------------------
        | Application IS Installed
        |--------------------------------------------------------------------------
        */

        // Allow DB repair page even after installation (rare edge case)
        if ($isSetupRepair) {
            return $next($request);
        }

        // Block all setup routes after installation
        if ($request->is('setup*')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'redirect' => '/admin/sign-in']);
            }
            return redirect('/admin/dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Database Integrity (only when app is installed)
        |--------------------------------------------------------------------------
        */
        try {

            DB::connection()->getPdo();

            if (!Schema::hasTable('integration_settings')) {
                return redirect()->route('setup.database-repair');
            }

            $installedInDb = DB::table('integration_settings')
                ->where('integration', 'app')
                ->where(function ($query) {
                    $query->where('is_setup', true)
                          ->orWhere('is_setup', 1);
                })
                ->exists();

            if (!$installedInDb) {
                return redirect()->route('setup.database-repair');
            }

        } catch (\Exception $e) {
            return redirect()->route('setup.database-repair');
        }

        return $next($request);
    }
}
