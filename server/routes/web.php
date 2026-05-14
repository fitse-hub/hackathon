<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All API endpoints are now in routes/api.php (prefixed with /api).
| This file only contains web/view routes.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Quick health-check (no auth needed)
Route::get('/test-connection', function () {
    return response()->json([
        'success'   => true,
        'message'   => '✅ Backend connected successfully!',
        'timestamp' => now()->toDateTimeString(),
        'server'    => 'Laravel ' . app()->version(),
        'php'       => PHP_VERSION,
    ]);
});
