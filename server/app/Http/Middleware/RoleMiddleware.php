<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        if (! in_array($user->role, $roles, true)) {
            return response()->json([
                'success'        => false,
                'message'        => 'Access denied. Insufficient permissions.',
                'your_role'      => $user->role,
                'required_roles' => $roles,
            ], 403);
        }

        return $next($request);
    }
}
