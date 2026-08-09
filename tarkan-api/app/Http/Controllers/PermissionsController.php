<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class PermissionsController extends Controller{


    public function get(Request $request){

        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar] = $auth;

        $qs = $request->query();

        $response = $traccar->getPermissions($qs, self::cookieAuth($request));

        return response($response->body(), $response->status());
    }


    public function post(Request $request){
        $data = $request->all();

        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        if(isset($data['deviceId']) && isset($data['driverId'])){
            $drivers = $traccar->getDrivers(self::cookieAuth($request));
            $foundDriver = null;

            if($drivers->status()==200){
                foreach($drivers->json() as $driver){
                    if($driver['id'] === $data['driverId']){
                        $foundDriver = $driver;
                        break;
                    }
                }
            }

            if($foundDriver && $foundDriver['attributes'] && $foundDriver['attributes']['tarkan.driverUserId']){
                $traccar->linkObjects(['userId'=>$foundDriver['attributes']['tarkan.driverUserId'],'deviceId'=>$data['deviceId']]);

                $checkAttributes = ComputedController::checkComputed($request);

                $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRCheckAlarm']]);
                $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRLockInfo']]);
                $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRDriverID']]);
                $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRLockAlarm']]);
            }
        }

        $response = $traccar->linkObjects($data, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 901, $response->status(), [
            'object' => $data,
            'data' => $data
        ]);

        return response($response->body(), $response->status());
    }

    public function delete(Request $request){
        $data = $request->all();

        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $response = $traccar->unlinkObjects($data, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 902, $response->status(), [
            'object' => $data,
            'data' => $data
        ]);

        return response($response->body(), $response->status());
    }

    public function postBulk(Request $request){
        $data = $request->all();

        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $response = $traccar->postPermissionsBulk($data, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 903, $response->status(), [
            'object' => $data,
            'data' => $data
        ]);

        return response($response->body(), $response->status());
    }

    public function deleteBulk(Request $request){
        $data = $request->all();

        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $response = $traccar->deletePermissionsBulk($data, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 904, $response->status(), [
            'object' => $data,
            'data' => $data
        ]);

        return response($response->body(), $response->status());
    }

}
