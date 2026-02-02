<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ManagerMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->isManager()) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
