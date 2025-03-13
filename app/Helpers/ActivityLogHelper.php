<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;

class ActivityLogHelper
{
    public static function logActivity($user, $activity, $details = null)
    {
        $request = request();
        $agent = new Agent();

        ActivityLog::create([
            'user_id' => $user?->id,
            'activity' => $activity,
            'details' => $details,
            'ip_address' => $request->ip(),
            'device' => $agent->device() . ' - ' . $agent->platform() . ' - ' . $agent->browser(),
        ]);
    }
    public static function getAllTransactionLogs()
    {
        return ActivityLog::where('activity', 'Completed a payment')->with('user')->orderBy('created_at', 'desc')->get();
    }

    public static function getAllActivityLogs()
    {
        return ActivityLog::with('user')->orderBy('created_at', 'desc')->get();
    }
}
