<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    /**
     * Get session information
     */
    public function getSessionInfo(Request $request)
    {
        return response()->json([
            'success' => true,
            'session_id' => $request->session()->getId(),
            'session_data' => $request->session()->all(),
            'csrf_token' => csrf_token(),
            'is_started' => $request->session()->isStarted(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Set session data
     */
    public function setSessionData(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required'
        ]);

        $request->session()->put($request->key, $request->value);

        return response()->json([
            'success' => true,
            'message' => 'Session data set successfully',
            'key' => $request->key,
            'value' => $request->value,
            'session_id' => $request->session()->getId()
        ]);
    }

    /**
     * Get session data by key
     */
    public function getSessionData(Request $request, $key)
    {
        $value = $request->session()->get($key);

        return response()->json([
            'success' => true,
            'key' => $key,
            'value' => $value,
            'exists' => $request->session()->has($key),
            'session_id' => $request->session()->getId()
        ]);
    }

    /**
     * Clear session data
     */
    public function clearSession(Request $request)
    {
        $sessionId = $request->session()->getId();
        $request->session()->flush();

        return response()->json([
            'success' => true,
            'message' => 'Session cleared successfully',
            'previous_session_id' => $sessionId,
            'new_session_id' => $request->session()->getId()
        ]);
    }

    /**
     * Get active sessions count
     */
    public function getActiveSessions()
    {
        $activeSessionsCount = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->count();

        $totalSessions = DB::table('sessions')->count();

        return response()->json([
            'success' => true,
            'active_sessions' => $activeSessionsCount,
            'total_sessions' => $totalSessions,
            'session_lifetime_minutes' => config('session.lifetime'),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Clean expired sessions
     */
    public function cleanExpiredSessions()
    {
        $expiredCount = DB::table('sessions')
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->count();

        DB::table('sessions')
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expired sessions cleaned',
            'cleaned_sessions' => $expiredCount,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
