<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class CommandsController extends Controller{

    public function send(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $data = $request->all();
        if(is_array($data['attributes'])){
            $data['attributes'] = (object) null;
        }

        $send = $traccar->sendCommand($data, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 201, $send->status(), [
            'object'=>['deviceId'=>(isset($data['deviceId']))?intval($data['deviceId']):0],
            'command'=>$request->all()
        ]);

        return response($send->body(),$send->status());

    }


}
