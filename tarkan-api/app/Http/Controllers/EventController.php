<?php


namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class EventController extends Controller{
    public function handleEvent(Request $request){


        $traccar = self::traccar($request);

        $event = $request->json('event');


        if($event['type']=='alarm'){

            $device = $request->json('device');

            if($event['attributes']['alarm']=='unknownDriver'){

                unset($device['attributes']['qrLockTime']);

                $device['attributes']['isQrLocked'] = true;

                $device = $traccar->saveDevice($device['id'], $device);

                sleep(1);

                $send = DeviceController::stopEngine($request, $device['id']);

                UserLog::record($request, '!Sistema!', 211, $send['status'], [
                    'object' => ['deviceId'=>$device['id']],
                    'native' => $send['native'],
                    'command' => $send['body'],
                    'sesId' => '',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!'
                ]);

            }else if($event['attributes']['alarm']=='checkoutDriver'){

                $foundDriver = null;

                $users = $request->json('users');

                foreach($users as $driver){
                    if(isset($driver['attributes']['tarkan.isQrDriverId']) && $driver['attributes']['tarkan.isQrDriverId'] == $device['attributes']['qrDriverId']){
                        $foundDriver = $driver;
                        break;
                    }
                }

                if($foundDriver) {

                    unset($foundDriver['attributes']['tarkan.isQrDeviceId']);
                    unset($device['attributes']['qrDriverId']);
                    unset($device['attributes']['qrCheckoutTime']);

                    $device = $traccar->saveDevice($device['id'], $device);
                    $user = $traccar->updateUser($foundDriver['id'], $foundDriver);
                }
            }


        }else if($event['type']=='ignitionOn'){

            $device = $request->json('device');
            $position = $request->json('position');

            if(isset($device['attributes']['tarkan.driverLockDevice']) && !isset($device['attributes']['qrDriverId'])  && $device['attributes']['tarkan.driverLockDevice']==1){
                $device['attributes']['qrLockTime'] = (strtotime($position['serverTime']) + ($device['attributes']['tarkan.lockDeviceTimeout'] * 60))*1000;
                $device = $traccar->saveDevice($device['id'],$device);
            }

        }else if($event['type']=='ignitionOff') {

            $device = $request->json('device');

            $foundDriver = null;

            if(isset($device['attributes']['qrDriverId'])){

                $users = $request->json('users');

                foreach($users as $driver){
                    if(isset($driver['attributes']['tarkan.isQrDriverId']) && $driver['attributes']['tarkan.isQrDriverId'] == $device['attributes']['qrDriverId']){
                        $foundDriver = $driver;
                        break;
                    }
                }

                if($foundDriver){

                    if(isset($foundDriver['attributes']['tarkan.driverAutoLogout']) && $foundDriver['attributes']['tarkan.driverAutoLogout']==1){

                        $position = $request->json('position');

                        $device['attributes']['qrCheckoutTime'] = (strtotime($position['serverTime']) + ($foundDriver['attributes']['tarkan.autoLogoutTimeout'] * 60))*1000;

                    }
                }
            }

            if(isset($device['attributes']['qrLockTime']) || $foundDriver) {
                unset($device['attributes']['qrLockTime']);
                $device = $traccar->saveDevice($device['id'], $device);
            }

        }else if($event['type']==='geofenceExit'){
            $geofence = $request->json('geofence');
            $device = $request->json('device');

            if((isset($device['attributes']['lockOnExit']) && $device['attributes']['lockOnExit']==true) ||
                (isset($geofence['attributes']['lockOnExit']) && $geofence['attributes']['lockOnExit']==true)){
                $send = DeviceController::stopEngine($request,$geofence['attributes']['deviceId']);

                $traccar->deleteGeofence($geofence['id']);

                UserLog::record($request, '!Sistema!', 202, $send['status'], [
                    'object' => ['deviceId'=>$geofence['attributes']['deviceId']],
                    'native' => $send['native'],
                    'command' => $send['body'],
                    'sesId' => '',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!'
                ]);
            }else if($geofence['attributes']['isAnchor'] && $geofence['attributes']['deviceId']){

                $send = DeviceController::stopEngine($request,$geofence['attributes']['deviceId']);

                $traccar->deleteGeofence($geofence['id']);

                UserLog::record($request, '!Sistema!', 201, $send['status'], [
                    'object' => ['deviceId'=>$geofence['attributes']['deviceId']],
                    'native' => $send['native'],
                    'command' => $send['body'],
                    'sesId' => '',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!'
                ]);

            }

        }else if($event['type']==='geofenceEnter'){
            $geofence = $request->json('geofence');
            $device = $request->json('device');

            if((isset($device['attributes']['lockOnEnter']) && $device['attributes']['lockOnEnter']==true) ||
                (isset($geofence['attributes']['lockOnEnter']) && $geofence['attributes']['lockOnEnter']==true)){
                $send = DeviceController::stopEngine($request,$geofence['attributes']['deviceId']);

                $traccar->deleteGeofence($geofence['id']);

                UserLog::record($request, '!Sistema!', 202, $send['status'], [
                    'object' => ['deviceId'=>$geofence['attributes']['deviceId']],
                    'native' => $send['native'],
                    'command' => $send['body'],
                    'sesId' => '',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!'
                ]);
            }

        }

        return response()->json(['success' => true]);
    }


}
