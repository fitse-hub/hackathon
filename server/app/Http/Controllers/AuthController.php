<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    //  POST /api/login
    // ─────────────────────────────────────────────────────────────────
    /**
     * Authenticate a user and return a Sanctum bearer token.
     */
    public function login(Request $request): JsonResponse
    {
        // --- validate -----------------------------------------------------------
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // --- attempt ------------------------------------------------------------
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials. Please check your email and password.',
            ], 401);
        }

        // --- issue token --------------------------------------------------------
        // Revoke any previous tokens so only one active session exists at a time.
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success'     => true,
            'message'     => 'Login successful',
            'user'        => $this->formatUser($user),
            'permissions' => $this->getPermissions($user->role),
            'token'       => $token,
            'token_type'  => 'Bearer',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  POST /api/logout
    // ─────────────────────────────────────────────────────────────────
    /**
     * Revoke the current access token (log out).
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete the token that was used for this request.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  GET /api/me
    // ─────────────────────────────────────────────────────────────────
    /**
     * Return the currently authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success'     => true,
            'user'        => $this->formatUser($user),
            'permissions' => $this->getPermissions($user->role),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  GET /api/roles  (public)
    // ─────────────────────────────────────────────────────────────────
    /**
     * Return the list of available roles.
     */
    public function getRoles(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'roles'   => User::getRoles(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Format a user for JSON output.
     */
    private function formatUser(User $user): array
    {
        return [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ];
    }

    /**
     * Build the permissions map for a given role.
     *
     * Manager     → full access
     * Sales Officer → can create sales, cannot approve, cannot manage users
     */
    private function getPermissions(string $role): array
    {
        $map = [
            User::ROLE_MANAGER => [
                'can_manage_users'  => true,
                'can_create_sales'  => true,
                'can_approve_sales' => true,
                'can_view_reports'  => true,
                'can_manage_settings' => true,
                'full_access'       => true,
            ],
            User::ROLE_SALES_OFFICER => [
                'can_manage_users'  => false,
                'can_create_sales'  => true,
                'can_approve_sales' => false,
                'can_view_reports'  => false,
                'can_manage_settings' => false,
                'full_access'       => false,
            ],
        ];

        return $map[$role] ?? $map[User::ROLE_SALES_OFFICER];
    }
}