<?php

namespace Arden28\Guardian\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $guard = 'api')
    {
        if (!Auth::guard($guard)->check() || !Auth::guard($guard)->user()->hasRole($role, $guard)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}