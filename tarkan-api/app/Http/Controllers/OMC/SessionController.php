<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class SessionController extends Controller{

    public function post(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');
        if(!isset($clientCookie)){
            $clientCookie = time();
        }

        $traccar = self::traccar($request);

        $me = $traccar->postSession(['email'=>trim($request->email),'password'=>trim($request->password)]);

        $cookie = $me->cookies()->getCookieByName('JSESSIONID');

        UserLog::record($request, $request->email, 101, $me->status(), [
            'object' => ['userId'=>(isset($me['id']))?intval($me['id']):0],
            'sesId' => $clientCookie
        ]);

        return response($me->body(),$me->status())->withCookie(cookie('JSESSIONID', ($cookie)?$cookie->getValue():'', ($cookie)?$cookie->getExpires():''));

    }


}
