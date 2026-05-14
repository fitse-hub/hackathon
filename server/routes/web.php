<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Simple test connection endpoint (no sessions needed)
Route::get('/test-connection', function () {
    return response()->json([
        'success' => true,
        'message' => '✅ Backend connected successfully!',
        'timestamp' => now()->toDateTimeString(),
        'server' => 'Laravel ' . app()->version(),
        'php_version' => PHP_VERSION,
        'database_sessions' => config('session.driver') === 'database' ? 'enabled' : 'disabled'
    ]);
});

// Controller-based endpoints
Route::get('/api/test', [ApiTestController::class, 'testConnection']);
Route::post('/api/echo', [ApiTestController::class, 'echo']);

// Authentication Routes
Route::prefix('api/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/roles', [AuthController::class, 'getRoles']);
});

// Protected Routes with Role-based Access Control
Route::middleware(['auth'])->prefix('api/admin')->group(function () {
    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', function () {
            return response()->json([
                'success' => true,
                'message' => 'Admin access granted - User management',
                'users' => \App\Models\User::all()
            ]);
        });
        
        Route::get('/system-settings', function () {
            return response()->json([
                'success' => true,
                'message' => 'Admin access granted - System settings',
                'settings' => ['maintenance_mode' => false, 'debug' => true]
            ]);
        });
    });
    
    // Manager and Admin routes
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/analytics', function () {
            return response()->json([
                'success' => true,
                'message' => 'Manager/Admin access granted - Analytics',
                'data' => ['views' => 1234, 'users' => 567, 'revenue' => 8900]
            ]);
        });
        
        Route::get('/content-management', function () {
            return response()->json([
                'success' => true,
                'message' => 'Manager/Admin access granted - Content management',
                'content' => ['posts' => 45, 'pages' => 12, 'media' => 234]
            ]);
        });
    });
    
    // Moderator, Manager and Admin routes
    Route::middleware(['role:admin,manager,moderator'])->group(function () {
        Route::get('/moderation', function () {
            return response()->json([
                'success' => true,
                'message' => 'Moderator+ access granted - Moderation tools',
                'pending_reports' => 3,
                'flagged_content' => 7
            ]);
        });
    });
    
    // All authenticated users
    Route::get('/profile', function () {
        return response()->json([
            'success' => true,
            'message' => 'User access granted - Profile management',
            'user' => auth()->user()
        ]);
    });
});

// Session Management Routes
Route::prefix('api/session')->group(function () {
    Route::get('/info', [SessionController::class, 'getSessionInfo']);
    Route::post('/set', [SessionController::class, 'setSessionData']);
    Route::get('/get/{key}', [SessionController::class, 'getSessionData']);
    Route::delete('/clear', [SessionController::class, 'clearSession']);
    Route::get('/active', [SessionController::class, 'getActiveSessions']);
    Route::delete('/cleanup', [SessionController::class, 'cleanExpiredSessions']);
});
