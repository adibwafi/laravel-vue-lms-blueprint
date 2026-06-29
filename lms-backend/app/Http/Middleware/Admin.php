<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle($request, Closure $next, $role, $guard = null)
    {
        if (!Auth::user()->hasAnyRole($role)) {
            return response()->json([
                'success' => false,
                'message' => 'Access not allowed!',
                'code' => 1,
                'data' => [],
            ], 403);
        }

        return $next($request);
    }
}
