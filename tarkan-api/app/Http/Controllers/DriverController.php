<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class DriverController extends Controller{

    public function post(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');


        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {

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
                    UserLog::create([
                        'sesId' => $clientCookie,
                        'serverIp' => $request->ip(),
                        'serverHost' => $request->header('tarkan-domain'),
                        'username' => $me['email'],
                        'userIp' => $request->header('x-real-ip'),
                        'userAgent' => $request->userAgent(),
                        'log' => [
                            'code' => 120,
                            'object' => ['userId' => $userCreation['id']],
                            'status' => $userCreation->status(),
                            'data' => $request->all()
                        ]
                    ]);


                    unset($data['attributes']['tarkan.driverPassword']);

                    $userData =$userCreation->json();

                    $data['attributes']['tarkan.driverUserId'] = $userData['id'];

                }else{
                    return response($userCreation->body(),401);
                }

            }

            if (is_array($data['attributes']) && count($data['attributes']) == 0) {
                $data['attributes'] = (object)null;
            }

            $driverCreation = $traccar->createDriver($data,['h'=>['Cookie'=>$request->headers->get('cookie')]]);

            if($driverCreation->status() === 200){
                UserLog::create([
                    'sesId' => $clientCookie,
                    'serverIp' => $request->ip(),
                    'serverHost' => $request->header('tarkan-domain'),
                    'username' => $me['email'],
                    'userIp' => $request->header('x-real-ip'),
                    'userAgent' => $request->userAgent(),
                    'log' => [
                        'code' => 510,
                        'object' => ['driverId' => $driverCreation['id']],
                        'status' => $driverCreation->status(),
                        'data' => $request->all()
                    ]
                ]);

                return response($driverCreation->body(),$driverCreation->status());

            }else{

                //REWORK -> FAZER DELETAR O USUÁRIO LINKADO CASO DE ERRO!

                return response($driverCreation->body(),401);
            }

        }else{
            return response('User not authed', 503);
        }


    }


    public function checkDriver(Request $request){
        $data = $request->json()->all();


        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);
        if($me->status()===200) {

            if($request->id==0){

                $userData = $me->json();


                $device = $traccar->getDevice($userData['attributes']['tarkan.isQrDeviceId'], ['h' => ['Cookie' => $request->headers->get('cookie')]]);
                $deviceData = $device->json();
                $deviceData = $deviceData[0];

                unset($userData['attributes']['tarkan.isQrDeviceId']);
                unset($deviceData['attributes']['qrDriverId']);

                $device = $traccar->saveDevice($deviceData['id'],$deviceData);
                $user = $traccar->updateUser($userData['id'],$userData);


                return response($user->body(), 200);
            }else {


                $device = $traccar->getDevice($request->id, ['h' => ['Cookie' => $request->headers->get('cookie')]]);



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


                        UserLog::create([
                            'sesId' => '',
                            'serverIp' => $request->ip(),
                            'serverHost' => $request->header('tarkan-domain'),
                            'username' => '!Sistema!',
                            'userIp' => '!Sistema!',
                            'userAgent' => '!Sistema!',
                            'log' => [
                                'code' => 212,
                                'object' => ['deviceId'=>$device['id']],
                                'status' => $send['status'],
                                'native' => $send['native'],
                                'command' => $send['body']
                            ]
                        ]);
                    }

                    return response($user->body(), 200);


                } else {
                    return response('Unauthorized', 503);
                }
            }

        }else{
            return response('User not authed', 503);
        }

    }





}

