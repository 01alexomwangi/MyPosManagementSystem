<?php

namespace App\Http\Middleware;

use Closure;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
     {
        // User must be logged in AND be admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Admins only');
        }

        return $next($request);
    }
    

}
