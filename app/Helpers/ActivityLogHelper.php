<?php

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Helper function to log user activities
 *
 * @param string $action The action being performed
 * @param string $description Description of the action
 * @param Request|null $request The request object (optional)
 * @return void
 */
function activity_log(string $action, string $description, ?Request $request = null): void
{
    try {
        $userId = Auth::id();
        
        if (!$userId) {
            return;
        }
        
        $log = new ActivityLog();
        $log->user_id = $userId;
        $log->action = $action;
        $log->description = $description;
        
        if ($request) {
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
        }
        
        $log->save();
    } catch (\Exception $e) {
        // Log the error to Laravel's log
        \Illuminate\Support\Facades\Log::error('Failed to create activity log: ' . $e->getMessage());
    }
}