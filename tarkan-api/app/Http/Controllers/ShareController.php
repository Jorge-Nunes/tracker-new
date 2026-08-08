<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ShareController extends Controller{

    public function getShares(Request $request){

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);


        if($me->status()===200){
            $getUsers = $traccar->getUsers();


            if($getUsers->status()===200){

                $shares = [];

                foreach($getUsers->json() as $user){
                    if(IsSet($user['attributes']['isShared']) && $user['attributes']['isShared'] == $me['id']){
                        $shares[] = [
                            "id"=>$user['id'],
                            "name"=>$user['name'],
                            "deviceId"=>intval($user['attributes']['deviceId']),
                            "expirationTime"=>$user['expirationTime'],
                            "limitCommands"=>$user['limitCommands'],
                            "token"=>$user['attributes']['token']
                        ];
                    }
                }

                return response()->json($shares);
            }else{
                return response()->json([],404);
            }
        }else{
            return response()->json([],503);
        }
    }

    public function createShare(Request $request){

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);
        if($me->status()===200){


            $getDevices = $traccar->getDevice($request->input('deviceId'),['h'=>['Cookie'=>$request->headers->get('cookie')]]);
            if($getDevices->status()===200){

                $tkn = Str::uuid()->toString();


                $createUser = $traccar->createUser([
                    "id"=>0,
                    "attributes"=>[
                        "isShared"=>$me['id'],
                        "deviceId"=>intval($request->input('deviceId')),
                        "token"=>$tkn
                    ],
                    "name"=>$request->input('name'),
                    "login"=>"share-".$me['id']."-".time(),
                    "email"=>$tkn,
                    "phone"=>"",
                    "readonly"=>false,
                    "administrator"=>false,
                    "map"=>"",
                    "latitude"=>0.0,
                    "longitude"=>0.0,
                    "zoom"=>0,
                    "coordinateFormat"=>"",
                    "disabled"=>false,
                    "expirationTime"=>$request->input('expirationTime'),
                    "deviceLimit"=>-1,
                    "userLimit"=>0,
                    "deviceReadonly"=>true,
                    "limitCommands"=>$request->input('limitCommands'),
                    "poiLayer"=>"",
                    "password"=>$tkn
                ]);

                $clientCookie = $request->cookie('JSESSIONID');

                UserLog::create([
                    'sesId' => $clientCookie,
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => $me['email'],
                    'userIp' => $request->header('x-real-ip'),
                    'userAgent' => $request->userAgent(),
                    'log' => [
                        'code' => 110,
                        'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
                        'status' => $createUser->status(),
                        'data' => $request->all()
                    ]
                ]);


                if($createUser->status()===200){
                    $user = $createUser->json();

                    $linkObject = $traccar->linkObjects(['userId'=>$user['id'],'deviceId'=>$request->input('deviceId')]);

                    return response()->json([
                        "id"=>$user['id'],
                        "name"=>$user['name'],
                        "deviceId"=>intval($user['attributes']['deviceId']),
                        "expirationTime"=>$user['expirationTime'],
                        "limitCommands"=>$user['limitCommands'],
                        "token"=>$user['attributes']['token']
                    ]);
                }else{
                    return response()->json($createUser->body(),401);
                }

            }else{
                return response()->json($getDevices->body(),404);
            }
        }else{
            return response()->json($me->body(),503);
        }
    }

    public function updateShare(Request $request){

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);



        if($me->status()===200){


            $getDevices = $traccar->getDevice($request->input('deviceId'),['h'=>['Cookie'=>$request->headers->get('cookie')]]);
            if($getDevices->status()===200){

                $tkn = $request->input('token');

                $createUser = $traccar->updateUser($request->shareId,[
                    "id"=>$request->shareId,
                    "attributes"=>[
                        "isShared"=>$me['id'],
                        "deviceId"=>intval($request->input('deviceId')),
                        "token"=>$tkn
                    ],
                    "name"=>$request->input('name'),
                    "login"=>"share-".$me['id']."-".time(),
                    "email"=>$tkn,
                    "phone"=>"",
                    "readonly"=>false,
                    "administrator"=>false,
                    "map"=>"",
                    "latitude"=>0.0,
                    "longitude"=>0.0,
                    "zoom"=>0,
                    "coordinateFormat"=>"",
                    "disabled"=>false,
                    "expirationTime"=>$request->input('expirationTime'),
                    "deviceLimit"=>-1,
                    "userLimit"=>0,
                    "deviceReadonly"=>true,
                    "limitCommands"=>$request->input('limitCommands'),
                    "poiLayer"=>"",
                    "password"=>$tkn
                ]);


                $clientCookie = $request->cookie('JSESSIONID');

                UserLog::create([
                    'sesId' => $clientCookie,
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => $me['email'],
                    'userIp' => $request->header('x-real-ip'),
                    'userAgent' => $request->userAgent(),
                    'log' => [
                        'code' => 111,
                        'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
                        'status' => $createUser->status(),
                        'data' => $request->all()
                    ]
                ]);



                if($createUser->status()===200){
                    $user = $createUser->json();

                    $linkObject = $traccar->linkObjects(['userId'=>$user['id'],'deviceId'=>$request->input('deviceId')]);

                    return response()->json([
                        "id"=>$user['id'],
                        "name"=>$user['name'],
                        "deviceId"=>intval($user['attributes']['deviceId']),
                        "expirationTime"=>$user['expirationTime'],
                        "limitCommands"=>$user['limitCommands'],
                        "token"=>$user['attributes']['token']
                    ]);
                }else{
                    return response()->json($createUser->body(),401);
                }

            }else{
                return response()->json($getDevices->body(),404);
            }
        }else{
            return response()->json($me->body(),503);
        }
    }

    public function deleteShare(Request $request){
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
                        'code' => 112,
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


    public function clearShare(Request $request){

        $traccar = new traccarConnector($request);
        $getUsers = $traccar->getUsers();

        if($getUsers->status()===200) {

            $shares = [];
            foreach ($getUsers->json() as $user) {
                if (isset($user['attributes']['isShared']) && (strtotime($user['expirationTime']) + (30 * (24 * 3600))) <= (time())) {
                    $shares[] = [
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "deviceId" => intval($user['attributes']['deviceId']),
                        "expirationTime" => $user['expirationTime'],
                        "limitCommands" => $user['limitCommands'],
                        "token" => $user['token']
                    ];

                    $deleteUser = $traccar->deleteUser($user['id']);

                    UserLog::create([
                        'sesId' => '!!SYSTEM!!',
                        'serverIp' => $request->ip(),
                        'serverHost' => $request->header('tarkan-domain'),
                        'username' => '!!!SYSTEM!!!',
                        'userIp' => '!!!SYSTEM!!!',
                        'userAgent' => $request->userAgent(),
                        'log' => [
                            'code' => 112,
                            'object' => ['userId' => $user['id']],
                            'status' => $deleteUser->status(),
                            'data' => $request->all()
                        ]
                    ]);

                }
            }


            return response()->json($shares);
        }else{
            return response()->json([],500);
        }
    }

}

