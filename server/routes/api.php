<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes — Phase 1 (Authentication + Role System)
|--------------------------------------------------------------------------
|
| POST   /api/login    → Authenticate & receive Bearer token
| POST   /api/logout   → Revoke current token  (auth required)
| GET    /api/me       → Current user profile   (auth required)
| GET    /api/roles    → List available roles    (public)
|
| Protected examples showing role-based access:
|
| Manager-only      → /api/manager/*
| All authenticated → /api/sales/*
|
*/

// ── Public ──────────────────────────────────────────────────────────────
Route::post('/auth/login',    [AuthController::class, 'login']);
Route::get('/roles',          [AuthController::class, 'getRoles']);

// ── Authenticated (any role) ────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // ── Manager-only routes ─────────────────────────────────────────
    Route::middleware('role:manager')->prefix('manager')->group(function () {

        Route::get('/users', function () {
            return response()->json([
                'success' => true,
                'message' => 'Manager access granted — User list',
                'users'   => \App\Models\User::select('id', 'name', 'email', 'role', 'created_at')->get(),
            ]);
        });

        Route::get('/dashboard', function () {
            return response()->json([
                'success' => true,
                'message' => 'Manager dashboard data',
                'stats'   => [
                    'total_users'    => \App\Models\User::count(),
                    'managers'       => \App\Models\User::where('role', 'manager')->count(),
                    'sales_officers' => \App\Models\User::where('role', 'sales_officer')->count(),
                ],
            ]);
        });
    });

    // ── Sales Officer routes (also accessible by manager) ───────────
    Route::middleware('role:manager,sales_officer')->prefix('sales')->group(function () {

        Route::get('/my-sales', function () {
            return response()->json([
                'success' => true,
                'message' => 'Your sales data',
                'sales'   => [],  // placeholder — will be filled in later phases
            ]);
        });
    });
});
