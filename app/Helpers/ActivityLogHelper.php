<?php

namespace App\Helpers;

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
            $logf->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
        }

        $log->save();
    } catch (\Exception $e) {
        // Mencatat kesalahan ke log Laravel
        \Illuminate\Support\Facades\Log::error('Gagal membuat log aktivitas: ' . $e->getMessage());
    }
}
