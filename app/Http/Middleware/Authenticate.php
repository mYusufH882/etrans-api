<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : abort(response()->json([
            'status' => 401,
            'error' => 'Unauthorized.'
        ], 401));
    }

    // protected function unauthenticated($request, array $guards)
    // {
    //     abort(response()->json([
    //         'status' => 401,
    //         'error' => 'Unauthorized.'
    //     ], 401));
    // }
}
