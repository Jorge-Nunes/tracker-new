<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class UserController extends Controller{

    public function post(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');


        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {



            $data = $request->all();
            if (IsSet($data['attributes']) && is_array($data['attributes']) && count($data['attributes'])==0) {
                $data['attributes'] = (object)null;
            }


            $notifications = $traccar->getAllNotifications();




            $user = $traccar->createUser($data, ['h' => ['Cookie' => $request->headers->get('cookie')]]);
            $userData = $user->json();


            foreach($notifications->json() as $n){
                if(IsSet($n['attributes']['tarkan.autoadd']) && $n['attributes']['tarkan.autoadd']==true){
                    $traccar->linkObjects(['userId'=>$userData['id'],'notificationId'=>$n['id']]);
                }
            }


            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => 101,
                    'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
                    'status' => $user->status(),
                    'data' => $request->all()
                ]
            ]);


            return response($user->body(), $user->status());

        } else {
            return response('User not authed', 503);
        }

    }

    public function put(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');


        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {

            $old = $traccar->getUsers($request->userId);


            $data = $request->all();
            if (IsSet($data['attributes']) && is_array($data['attributes']) && count($data['attributes'])==0) {
                $data['attributes'] = (object)null;
            }

            $user = $traccar->updateUser($request->userId, $data, ['h' => ['Cookie' => $request->headers->get('cookie')]]);


            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => 105,
                    'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
                    'status' => $user->status(),
                    'old' => $old->json(),
                    'data' => $request->all()
                ]
            ]);


            return response($user->body(), $user->status());

        } else {
            return response('User not authed', 503);
        }

    }


    public function delete(Request $request){
        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);
        if($me->status()===200){
            $getDevices = $traccar->getUsers($request->input('shareId'),['h'=>['Cookie'=>$request->headers->get('cookie')]]);
            if($getDevices->status()===200){


                $deleteUser = $traccar->deleteUser($request->shareId);

                $clientCookie = $request->cookie('JSESSIONID');

                UserLog::create([
                    'sesId' => $clientCookie,
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => $me['email'],
                    'userIp' => $request->header('x-real-ip'),
                    'userAgent' => $request->userAgent(),
                    'log' => [
                        'code' => 102,
                        'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
                        'status' => $deleteUser->status(),
                        'data' => $request->all()
                    ]
                ]);


                if($deleteUser->status()===204){
                    return response()->json(['success'=>true]);
                }else{
                    return response()->json($deleteUser->body(),401);
                }

            }else{
                return response()->json([],404);
            }
        }else{
            return response()->json([],503);
        }
    }


}

