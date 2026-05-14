<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:  ->middleware('role:manager')
     *                   ->middleware('role:manager,sales_officer')
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  One or more allowed roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Must be authenticated (Sanctum guard should have already caught this,
        // but we keep a safety check).
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        // Check if the user's role is among the allowed roles.
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
