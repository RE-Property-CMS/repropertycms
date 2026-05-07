<?php

namespace App\Http\Middleware;

use App\Models\DemoSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Demo branch only.
 * Enforces that a valid, non-expired demo session is active before
 * allowing access to agent or admin routes.
 */
class DemoMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Super admin is a permanent account — fully exempt from demo session requirements
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->is_super_admin) {
            return $next($request);
        }

        // Invited users arrive directly from the email link with no session token.
        // Auto-populate the session from whichever guard is authenticated.
        if (Auth::guard('admin')->check()) {
            $demoAdminToken = Auth::guard('admin')->user()->demo_session_id;
            if ($demoAdminToken && ! session('demo_session_id')) {
                session(['demo_session_id' => $demoAdminToken]);
            }
        } elseif (Auth::guard('agent')->check()) {
            $demoAgentToken = Auth::guard('agent')->user()->demo_session_id;
            if ($demoAgentToken && ! session('demo_session_id')) {
                session(['demo_session_id' => $demoAgentToken]);
            }
        }

        $token = session('demo_session_id');

        if (! $token) {
            return redirect('/demo')->with('info', 'Start a demo session to continue.');
        }

        $demoSession = DemoSession::where('token', $token)->first();

        if (! $demoSession || $demoSession->isExpired()) {
            Auth::guard('agent')->logout();
            Auth::guard('admin')->logout();
            $request->session()->forget([
                'demo_session_id',
                'admin',
                'agent',
                'property',
                'login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d',
            ]);
            return redirect('/demo?expired=1');
        }

        return $next($request);
    }
}
