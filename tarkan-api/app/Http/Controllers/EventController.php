<?php


namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EventController extends Controller{
    public function handleEvent(Request $request){


        $traccar = new traccarConnector($request);

        $event = $request->json('event');

        //$ah = Http::acceptJson();

        //$ah->post("https://webhook.site/915fd6ff-9297-4e20-91e8-cd32aad29c3e",$request->all());


        if($event['type']=='alarm'){

            $device = $request->json('device');

            if($event['attributes']['alarm']=='unknownDriver'){

                unset($device['attributes']['qrLockTime']);

                $device['attributes']['isQrLocked'] = true;

                $device = $traccar->saveDevice($device['id'], $device);

                sleep(1);

                $send = DeviceController::stopEngine($request, $device['id']);


                UserLog::create([
                    'sesId' => '',
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => '!Sistema!',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!',
                    'log' => [
                        'code' => 211,
                        'object' => ['deviceId'=>$device['id']],
                        'status' => $send['status'],
                        'native' => $send['native'],
                        'command' => $send['body']
                    ]
                ]);


            }else if($event['attributes']['alarm']=='checkoutDriver'){

                $foundDriver = null;

                $users = $request->json('users');



                foreach($users as $driver){
                    if(IsSet($driver['attributes']['tarkan.isQrDriverId']) && $driver['attributes']['tarkan.isQrDriverId'] == $device['attributes']['qrDriverId']){
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

            if(IsSet($device['attributes']['tarkan.driverLockDevice']) && !IsSet($device['attributes']['qrDriverId'])  && $device['attributes']['tarkan.driverLockDevice']==1){
                $device['attributes']['qrLockTime'] = (strtotime($position['serverTime']) + ($device['attributes']['tarkan.lockDeviceTimeout'] * 60))*1000;
                $device = $traccar->saveDevice($device['id'],$device);
            }

        }else if($event['type']=='ignitionOff') {

            //$ah->post("https://webhook.site/915fd6ff-9297-4e20-91e8-cd32aad29c3e",$event);

            $device = $request->json('device');

            $foundDriver = null;

            //$ah->post("https://webhook.site/915fd6ff-9297-4e20-91e8-cd32aad29c3e",$device);

            if(IsSet($device['attributes']['qrDriverId'])){


                $users = $request->json('users');



                foreach($users as $driver){
                    if(IsSet($driver['attributes']['tarkan.isQrDriverId']) && $driver['attributes']['tarkan.isQrDriverId'] == $device['attributes']['qrDriverId']){
                        $foundDriver = $driver;


                        break;
                    }
                }

                if($foundDriver){



                    if(IsSet($foundDriver['attributes']['tarkan.driverAutoLogout']) && $foundDriver['attributes']['tarkan.driverAutoLogout']==1){


                        //unset($foundDriver['attributes']['tarkan.isQrDeviceId']);
                        //unset($device['attributes']['qrDriverId']);


                        $position = $request->json('position');

                        $device['attributes']['qrCheckoutTime'] = (strtotime($position['serverTime']) + ($foundDriver['attributes']['tarkan.autoLogoutTimeout'] * 60))*1000;



                        //$user = $traccar->updateUser($foundDriver['id'],$foundDriver);

                    }
                }
            }

            if(IsSet($device['attributes']['qrLockTime']) || $foundDriver) {
                unset($device['attributes']['qrLockTime']);
                $device = $traccar->saveDevice($device['id'], $device);
            }


            //if(IsSet($event['device']['attributes']['tarkan.'])){
            //}


        }else if($event['type']==='geofenceExit'){
            $geofence = $request->json('geofence');
            $device = $request->json('device');

            if((IsSet($device['attributes']['lockOnExit']) && $device['attributes']['lockOnExit']==true) ||
                (IsSet($geofence['attributes']['lockOnExit']) && $geofence['attributes']['lockOnExit']==true)){
                $send = DeviceController::stopEngine($request,$geofence['attributes']['deviceId']);


                $traccar->deleteGeofence($geofence['id']);

                UserLog::create([
                    'sesId' => '',
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => '!Sistema!',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!',
                    'log' => [
                        'code' => 202,
                        'object' => ['deviceId'=>$geofence['attributes']['deviceId']],
                        'status' => $send['status'],
                        'native' => $send['native'],
                        'command' => $send['body']
                    ]
                ]);
            }else if($geofence['attributes']['isAnchor'] && $geofence['attributes']['deviceId']){

                $send = DeviceController::stopEngine($request,$geofence['attributes']['deviceId']);


                $traccar->deleteGeofence($geofence['id']);

                UserLog::create([
                    'sesId' => '',
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => '!Sistema!',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!',
                    'log' => [
                        'code' => 201,
                        'object' => ['deviceId'=>$geofence['attributes']['deviceId']],
                        'status' => $send['status'],
                        'native' => $send['native'],
                        'command' => $send['body']
                    ]
                ]);

            }

        }else if($event['type']==='geofenceEnter'){
            $geofence = $request->json('geofence');
            $device = $request->json('device');

            if((IsSet($device['attributes']['lockOnEnter']) && $device['attributes']['lockOnEnter']==true) ||
                (IsSet($geofence['attributes']['lockOnEnter']) && $geofence['attributes']['lockOnEnter']==true)){
                $send = DeviceController::stopEngine($request,$geofence['attributes']['deviceId']);


                $traccar->deleteGeofence($geofence['id']);

                UserLog::create([
                    'sesId' => '',
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => '!Sistema!',
                    'userIp' => '!Sistema!',
                    'userAgent' => '!Sistema!',
                    'log' => [
                        'code' => 202,
                        'object' => ['deviceId'=>$geofence['attributes']['deviceId']],
                        'status' => $send['status'],
                        'native' => $send['native'],
                        'command' => $send['body']
                    ]
                ]);
            }

        }

        return response()->json(['success' => true]);
    }


}
