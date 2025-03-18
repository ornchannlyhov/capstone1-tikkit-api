<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    // Display Transaction Logs (Web View)
    public function index()
    {
        $transactionLogs = ActivityLogHelper::getAllTransactionLogs();

        return view('admin.transaction_logs', ['transactionLogs' => $transactionLogs]);
    }

    // Display All Activity Logs (Web View)
    public function allLogs()
    {
        $activityLogs = ActivityLogHelper::getAllActivityLogs();

        return view('admin.activity_logs', ['activityLogs' => $activityLogs]);
    }
}