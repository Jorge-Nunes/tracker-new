<?php

namespace App\Http\Controllers;

use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use App\Models\UserLog;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class CommandsController extends Controller{

    public function send(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');


        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);
        if($me->status()===200) {

            $data = $request->all();
            if(is_array($data['attributes'])){
                $data['attributes'] = (object) null;
            }

            $send = $traccar->sendCommand($data,['h'=>['Cookie'=>$request->headers->get('cookie')]]);



            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => 201,
                    'object'=>['deviceId'=>(IsSet($data['deviceId']))?intval($data['deviceId']):0],
                    'status' => $send->status(),
                    'command'=>$request->all()
                ]
            ]);

            return response($send->body(),$send->status());

        }else {

            return response('User not authed', 503);
        }
    }


}

