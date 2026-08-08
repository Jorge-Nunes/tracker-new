<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class SessionController extends Controller{

    public function post(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');
        if(!IsSet($clientCookie)){
            $clientCookie = time();
        }



        $traccar = new traccarConnector($request);

        $me = $traccar->postSession(['email'=>trim($request->email),'password'=>trim($request->password)]);

        $cookie = $me->cookies()->getCookieByName('JSESSIONID');

        UserLog::create([
            'sesId'=>$clientCookie,
            'serverIp'=>$request->ip(),
            'serverHost'=>$request->header('tarkan-domain'),
            'username'=>$request->email,
            'userIp'=>$request->header('x-real-ip'),
            'userAgent'=>$request->userAgent(),
            'log'=>[
                'code'=>101,
                'object'=>['userId'=>(IsSet($me['id']))?intval($me['id']):0],
                'status'=>$me->status()
            ]
        ]);



        return response($me->body(),$me->status())->withCookie(cookie('JSESSIONID', ($cookie)?$cookie->getValue():'', ($cookie)?$cookie->getExpires():''));

    }


}

