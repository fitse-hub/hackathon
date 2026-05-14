<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    public function testConnection()
    {
        return response()->json([
            'success' => true,
            'message' => '✅ Backend API is working perfectly!',
            'timestamp' => now()->toDateTimeString(),
            'server' => 'Laravel ' . app()->version(),
            'php_version' => PHP_VERSION
        ]);
    }

    public function echo(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Echo endpoint working',
            'received_data' => $request->all(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}

