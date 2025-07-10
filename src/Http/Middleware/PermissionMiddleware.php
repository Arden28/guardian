<?php

namespace Arden28\Guardian\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permission
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $permission, $guard = 'api')
    {
        if (!Auth::guard($guard)->check() || !Auth::guard($guard)->user()->hasPermissionTo($permission, $guard)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}