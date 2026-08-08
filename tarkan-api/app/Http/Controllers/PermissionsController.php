<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class PermissionsController extends Controller{


    public function get(Request $request){

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {

            $qs = $request->query();

            $response = $traccar->getPermissions($qs, ['h' => ['Cookie' => $request->headers->get('cookie')]]);

            return response($response->body(), $response->status());
        } else {
            return response('User not authed', 503);
        }
    }


    public function post(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');

        $data = $request->all();


        $traccar = new traccarConnector($request);


        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {

            if(IsSet($data['deviceId']) && IsSet($data['driverId'])){
                    $drivers = $traccar->getDrivers(['h' => ['Cookie' => $request->headers->get('cookie')]]);
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
                        $response = $traccar->linkObjects(['userId'=>$foundDriver['attributes']['tarkan.driverUserId'],'deviceId'=>$data['deviceId']]);

                        $checkAttributes = ComputedController::checkComputed($request);


                        $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRCheckAlarm']]);
                        $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRLockInfo']]);
                        $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRDriverID']]);
                        $traccar->linkObjects(['deviceId'=>$data['deviceId'],'attributeId'=>$checkAttributes['tarkan.QRLockAlarm']]);


                        //dd($response->status());


                    }


            }


            $response = $traccar->linkObjects($data,['h' => ['Cookie' => $request->headers->get('cookie')]]);


            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => 901,
                    'object' => $data,
                    'status' => $response->status(),
                    "data" => $data
                ]
            ]);


            return response($response->body(), $response->status());
        } else {
            return response('User not authed', 503);
        }
    }

    public function delete(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');

        $data = $request->all();

        //dd($data);

        $traccar = new traccarConnector($request);


        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {


            $response = $traccar->unlinkObjects($data,['h' => ['Cookie' => $request->headers->get('cookie')]]);


            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => 902,
                    'object' => $data,
                    'status' => $response->status(),
                    "data" => $data
                ]
            ]);


            return response($response->body(), $response->status());
        } else {
            return response('User not authed', 503);
        }
    }

    public function postBulk(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');

        $data = $request->all();

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {

            $response = $traccar->postPermissionsBulk($data, ['h' => ['Cookie' => $request->headers->get('cookie')]]);

            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => 903,
                    'object' => $data,
                    'status' => $response->status(),
                    "data" => $data
                ]
            ]);

            return response($response->body(), $response->status());
        } else {
            return response('User not authed', 503);
        }
    }

    public function deleteBulk(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');

        $data = $request->all();

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h' => ['Cookie' => $request->headers->get('cookie')]]);
        if ($me->status() === 200) {

            $response = $traccar->deletePermissionsBulk($data, ['h' => ['Cookie' => $request->headers->get('cookie')]]);

            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => 904,
                    'object' => $data,
                    'status' => $response->status(),
                    "data" => $data
                ]
            ]);

            return response($response->body(), $response->status());
        } else {
            return response('User not authed', 503);
        }
    }

}

