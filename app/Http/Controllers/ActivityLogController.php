<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    // Display Transaction Logs (Web View)
    public function index()
    {
        $transactionLogs = ActivityLog::paginate(10);

        return view('admin.transaction_logs', ['transactionLogs' => $transactionLogs]);
    }


// Removed redundant code outside of a function

    // Display All Activity Logs (Web View)
    public function allLogs()
    {
        $activityLogs = ActivityLog::paginate(10);

        return view('admin.activity_logs', ['activityLogs' => $activityLogs]);
    }
}