<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogsController extends Controller{
    public function clearLogs(Request $request){
       return response()->json(UserLog::whereDate( 'created_at', '<=', Carbon::now()->subDays(30))->delete());
    }

    public function deviceLogs(Request $request){
        return response()->json(UserLog::forDomain($request->header('tarkan-domain'))
            ->whereRaw('JSON_CONTAINS(log->"$.object", \'{"deviceId":'.intval($request->deviceId).'}\')')
            ->orderBy('created_at')
            ->get());
    }

    public function serverLogs(Request $request){
        return response()->json(UserLog::forDomain($request->header('tarkan-domain'))->orderBy('created_at')->get());
    }

    public function userLogs(Request $request){
        $traccar = self::traccar($request);

        $user = $traccar->getUsers($request->userId);
        if($user->status()===200){
            return response()->json(UserLog::where(['serverHost'=>$request->header('tarkan-domain'),'username' => $user['email']])->orderBy('created_at')->get());
        }

        return response($user->body(),$user->status());
    }
}
