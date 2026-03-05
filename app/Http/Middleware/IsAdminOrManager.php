<?php

namespace App\Http\Middleware;

use Closure;

class IsAdminOrManager
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
          if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admins and Managers only.'
            ], 403);
        }

        return $next($request);
    }
}
