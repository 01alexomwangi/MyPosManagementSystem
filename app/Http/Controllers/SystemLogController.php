<?php

namespace App\Http\Controllers;

use App\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function logs()
    {
        $logs = SystemLog::latest()->paginate(20);
        return view('admin.logs', compact('logs'));
    }
}
