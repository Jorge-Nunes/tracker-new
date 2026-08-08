<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogsController extends Controller{
    public function clearLogs(Request $request){
       return response()->json(UserLog::whereDate( 'created_at', '<=', Carbon::now()->subDays(30))->delete());
    }
}
