<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShareController extends Controller{

    public function getShares(Request $request){

        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));

        if($me->status()!==200){
            return response()->json([],503);
        }

        $getUsers = $traccar->getUsers();

        if($getUsers->status()!==200){
            return response()->json([],404);
        }

        $shares = [];

        foreach($getUsers->json() as $user){
            if(isset($user['attributes']['isShared']) && $user['attributes']['isShared'] == $me['id']){
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
    }

    public function createShare(Request $request){

        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));
        if($me->status()!==200){
            return response()->json($me->body(),503);
        }

        return $this->saveShare($request, $traccar, $me, 0, Str::uuid()->toString(), 110);
    }

    public function updateShare(Request $request){

        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));
        if($me->status()!==200){
            return response()->json($me->body(),503);
        }

        return $this->saveShare($request, $traccar, $me, $request->shareId, $request->input('token'), 111);
    }

    public function deleteShare(Request $request){
        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));
        if($me->status()!==200){
            return response()->json([],503);
        }

        $getDevices = $traccar->getUsers($request->input('shareId'), self::cookieAuth($request));
        if($getDevices->status()!==200){
            return response()->json([],404);
        }

        $deleteUser = $traccar->deleteUser($request->shareId);

        UserLog::record($request, $me['email'], 112, $deleteUser->status(), [
            'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0]
        ]);

        if($deleteUser->status()===204){
            return response()->json(['success'=>true]);
        }

        return response()->json($deleteUser->body(),401);
    }


    public function clearShare(Request $request){

        $traccar = self::traccar($request);
        $getUsers = $traccar->getUsers();

        if($getUsers->status()!==200) {
            return response()->json([],500);
        }

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

                UserLog::record($request, '!!!SYSTEM!!!', 112, $deleteUser->status(), [
                    'object' => ['userId' => $user['id']],
                    'data' => $request->all(),
                    'sesId' => '!!SYSTEM!!',
                    'userIp' => '!!!SYSTEM!!!'
                ]);

            }
        }


        return response()->json($shares);
    }

    private function saveShare(Request $request, traccarConnector $traccar, $me, $userId, $token, $code){

        $getDevices = $traccar->getDevice($request->input('deviceId'), self::cookieAuth($request));
        if($getDevices->status()!==200){
            return response()->json($getDevices->body(),404);
        }

        $payload = [
            "id"=>$userId,
            "attributes"=>[
                "isShared"=>$me['id'],
                "deviceId"=>intval($request->input('deviceId')),
                "token"=>$token
            ],
            "name"=>$request->input('name'),
            "login"=>"share-".$me['id']."-".time(),
            "email"=>$token,
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
            "password"=>$token
        ];

        $createUser = ($userId === 0)
            ? $traccar->createUser($payload, self::cookieAuth($request))
            : $traccar->updateUser($userId, $payload, self::cookieAuth($request));

        UserLog::record($request, $me['email'], $code, $createUser->status(), [
            'object' => ['userId' => (isset($request->userId)) ? intval($request->userId) : 0],
            'data' => $request->all()
        ]);

        if($createUser->status()===200){
            $user = $createUser->json();

            $traccar->linkObjects(['userId'=>$user['id'],'deviceId'=>$request->input('deviceId')]);

            return response()->json([
                "id"=>$user['id'],
                "name"=>$user['name'],
                "deviceId"=>intval($user['attributes']['deviceId']),
                "expirationTime"=>$user['expirationTime'],
                "limitCommands"=>$user['limitCommands'],
                "token"=>$user['attributes']['token']
            ]);
        }

        return response()->json($createUser->body(),401);
    }

}
