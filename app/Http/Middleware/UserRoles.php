<?php

namespace App\Http\Middleware;

use Closure;

class UserRoles
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        return collect($roles)->contains(auth()->user()->role) ? $next($request) : back();
    }
}
