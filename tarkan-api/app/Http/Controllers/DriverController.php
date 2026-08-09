<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class DriverController extends Controller{

    public function post(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $data = $request->toArray();

        if (isset($data['attributes']['tarkan.enableQrCode']) && $data['attributes']['tarkan.enableQrCode'] == true) {

            $driverUsername = $data['attributes']['tarkan.driverUsername'];
            $driverPassword = $data['attributes']['tarkan.driverPassword'];

            $att = $data['attributes'];

            $att["tarkan.isQrDriverId"] = $data['uniqueId'];

            unset($att['tarkan.driverPassword']);

            $userCreation = $traccar->createUser([
                "id"=>0,
                "attributes"=>$att,
                "name"=>$data['name'],
                "login"=>"qrcode-".$driverUsername,
                "email"=>"qrcode-".$driverUsername,
                "phone"=>"",
                "readonly"=>true,
                "administrator"=>false,
                "map"=>"",
                "latitude"=>0.0,
                "longitude"=>0.0,
                "zoom"=>0,
                "coordinateFormat"=>"",
                "disabled"=>false,
                "expirationTime"=>null,
                "deviceLimit"=>-1,
                "userLimit"=>0,
                "deviceReadonly"=>true,
                "limitCommands"=>true,
                "poiLayer"=>"",
                "password"=>$driverPassword
            ]);

            if($userCreation->status()===200){
                UserLog::record($request, $me['email'], 120, $userCreation->status(), [
                    'object' => ['userId' => $userCreation['id']],
                    'data' => $request->all()
                ]);

                unset($data['attributes']['tarkan.driverPassword']);

                $userData = $userCreation->json();

                $data['attributes']['tarkan.driverUserId'] = $userData['id'];
            }else{
                return response($userCreation->body(),401);
            }

        }

        if (is_array($data['attributes']) && count($data['attributes']) == 0) {
            $data['attributes'] = (object)null;
        }

        $driverCreation = $traccar->createDriver($data, self::cookieAuth($request));

        if($driverCreation->status() === 200){
            UserLog::record($request, $me['email'], 510, $driverCreation->status(), [
                'object' => ['driverId' => $driverCreation['id']],
                'data' => $request->all()
            ]);

            return response($driverCreation->body(),$driverCreation->status());
        }

        return response($driverCreation->body(),401);

    }


    public function checkDriver(Request $request){
        $data = $request->json()->all();

        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));
        if($me->status()!==200) {
            return response('User not authed', 503);
        }

        if($request->id==0){

            $userData = $me->json();

            $device = $traccar->getDevice($userData['attributes']['tarkan.isQrDeviceId'], self::cookieAuth($request));
            $deviceData = $device->json();
            $deviceData = $deviceData[0];

            unset($userData['attributes']['tarkan.isQrDeviceId']);
            unset($deviceData['attributes']['qrDriverId']);

            $device = $traccar->saveDevice($deviceData['id'],$deviceData);
            $user = $traccar->updateUser($userData['id'],$userData);

            return response($user->body(), 200);
        }

        $device = $traccar->getDevice($request->id, self::cookieAuth($request));

        if ($device->status() === 200) {

            $userData = $me->json();
            $deviceData = $device->json();
            $deviceData = $deviceData[0];

            if (is_array($userData['attributes']) && count($userData['attributes']) == 0) {
                $userData['attributes'] = (object)null;
            }

            if (is_array($deviceData['attributes']) && count($deviceData['attributes']) == 0) {
                $deviceData['attributes'] = (object)null;
            }

            $userData['attributes']['tarkan.isQrDeviceId'] = $request->id;
            $deviceData['attributes']['qrDriverId'] = $userData['attributes']['tarkan.isQrDriverId'];

            unset($deviceData['attributes']['qrLockTime']);

            $device = $traccar->saveDevice($request->id, $deviceData);
            $user = $traccar->updateUser($userData['id'], $userData);

            if (isset($device['attributes']['isQrLocked']) && $device['attributes']['isQrLocked']==true && isset($userData['attributes']['tarkan.driverUnlockDevice']) && $userData['attributes']['tarkan.driverUnlockDevice'] == true) {
                $send = DeviceController::resumeEngine($request, $request->id);

                UserLog::record($request, '!Sistema!', 212, $send['status'], [
                    'object' => ['deviceId'=>$device['id']],
                    'native' => $send['native'],
                    'command' => $send['body'],
                    'sesId' => '',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!'
                ]);
            }

            return response($user->body(), 200);
        }

        return response('Unauthorized', 503);
    }

}
