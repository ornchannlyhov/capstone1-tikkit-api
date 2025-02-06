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
            'user_id' => $user->id,
            'activity' => $activity,
            'details' => $details,
            'ip_address' => $request->ip(),
            'device' => $agent->device() . ' - ' . $agent->platform() . ' - ' . $agent->browser(),
            'date' => now(),
        ]);
    }
}
