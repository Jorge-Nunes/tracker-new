<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServerController extends Controller{

    public function get(Request $request){
        $traccar = self::traccar($request);
        $devices = $traccar->getDevices();

        $devicesFiltered = array_filter($devices->json(),function($a){
            $uniqueId = explode("-",$a['uniqueId']);


            return $uniqueId[0]!=='deleted';
        });

        $server = $traccar->getServer();

        $svJSON = $server->json();

        $deviceLimit = self::remainingDeviceLimit($svJSON, count($devicesFiltered));

        return response($server->body(),$server->status())->header('licensemode','TarkanPlus')->header('deviceLimit',$deviceLimit);
    }

    public function put(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar] = $auth;

        $server = $traccar->putServer($request->input());

        $devices = $traccar->getDevices();
        $svJSON = $server->json();
        $deviceLimit = self::remainingDeviceLimit($svJSON, count($devices->json()));

        return response($server->body(), $server->status())->header('licensemode', 'TarkanPlus')->header('deviceLimit', $deviceLimit);
    }


    public function restartServer(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        shell_exec('sleep 5 && /sbin/reboot > /dev/null 2>&1 &');

        return response()->json([]);
    }

    private static function remainingDeviceLimit($svJSON, $deviceCount){
        $limit = isset($svJSON['attributes']['tarkan.deviceLimit']) ? $svJSON['attributes']['tarkan.deviceLimit'] : null;

        if($limit !== null && $limit >= 0){
            return $limit - $deviceCount;
        }

        return false;
    }


}
