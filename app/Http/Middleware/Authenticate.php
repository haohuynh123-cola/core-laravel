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
        if ($request->expectsJson()) {
            return response()->json(['message' => "Token is expired"], 401);
        }
        if (!$request->expectsJson()) {
            return response()->json(['message' => "You Not Permission"], 403);
        }
        // return $request->expectsJson() ? null : route('login');
    }
}