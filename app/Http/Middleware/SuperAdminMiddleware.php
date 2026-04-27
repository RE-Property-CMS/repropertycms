<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth('admin')->check() || ! auth('admin')->user()->is_super_admin) {
            abort(403, 'This area is restricted to the system owner only.');
        }

        return $next($request);
    }
}
