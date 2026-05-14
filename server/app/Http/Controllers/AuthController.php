<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'permissions' => $this->getUserPermissions($user->role),
                'session_id' => $request->session()->getId()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,user,manager,moderator',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'permissions' => $this->getUserPermissions($user->role),
            'session_id' => $request->session()->getId()
        ], 201);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $sessionId = $request->session()->getId();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
            'previous_session_id' => $sessionId
        ]);
    }

    /**
     * Get current user info
     */
    public function me(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'permissions' => $this->getUserPermissions($user->role),
            'session_id' => $request->session()->getId()
        ]);
    }

    /**
     * Get user permissions based on role
     */
    private function getUserPermissions($role)
    {
        $permissions = [
            'admin' => [
                'can_manage_users' => true,
                'can_manage_content' => true,
                'can_view_analytics' => true,
                'can_manage_settings' => true,
                'can_delete_content' => true,
                'can_moderate' => true,
            ],
            'manager' => [
                'can_manage_users' => false,
                'can_manage_content' => true,
                'can_view_analytics' => true,
                'can_manage_settings' => false,
                'can_delete_content' => true,
                'can_moderate' => true,
            ],
            'moderator' => [
                'can_manage_users' => false,
                'can_manage_content' => true,
                'can_view_analytics' => false,
                'can_manage_settings' => false,
                'can_delete_content' => false,
                'can_moderate' => true,
            ],
            'user' => [
                'can_manage_users' => false,
                'can_manage_content' => false,
                'can_view_analytics' => false,
                'can_manage_settings' => false,
                'can_delete_content' => false,
                'can_moderate' => false,
            ],
        ];

        return $permissions[$role] ?? $permissions['user'];
    }

    /**
     * Get all available roles
     */
    public function getRoles()
    {
        return response()->json([
            'success' => true,
            'roles' => User::getRoles()
        ]);
    }
}