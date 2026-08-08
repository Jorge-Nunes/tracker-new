<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Tarkan\traccarConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class GeofenceController extends Controller{

    public function post(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);
        if($me->status()===200) {

            $data = $request->all();

            if (is_array($data['attributes']) && count($data['attributes'])===0) {
                $data['attributes'] = (object)null;
            }

            $attributes = $request->json('attributes');


            $geofence = $traccar->createGeofence($data,['h'=>['Cookie'=>$request->headers->get('cookie')]]);

            if(IsSet($attributes['isAnchor'])){
                $objecto = ['deviceId'=>$attributes['deviceId']];
                $code = 405;
            }else{


                $objecto = ['geofenceId'=>$geofence->json('id')];
                $code = 401;
            }


            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => $code,
                    'object' => $objecto,
                    'status' => $geofence->status(),
                    "data" => $data
                ]
            ]);

            return response($geofence->body(),$geofence->status());
        }else{
            return response($me->body(),$me->status());
        }
    }

    public function delete(Request $request){
        $clientCookie = $request->cookie('JSESSIONID');

        $traccar = new traccarConnector($request);

        $me = $traccar->getSession(['h'=>['Cookie'=>$request->headers->get('cookie')]]);
        if($me->status()===200) {

            $geofenceData = $traccar->getGeofences($request->geofenceId,['h'=>['Cookie'=>$request->headers->get('cookie')]]);
            $geofenceData = $geofenceData->json();


            $geofence = $traccar->deleteGeofence($request->geofenceId,['h'=>['Cookie'=>$request->headers->get('cookie')]]);

            if(IsSet($geofenceData['attributes']['isAnchor'])){
                $objecto = ['deviceId'=>$geofenceData['attributes']['deviceId']];
                $code = 406;
            }else{
                $objecto = ['geofenceId'=>$request->geofenceId];
                $code = 402;
            }


            UserLog::create([
                'sesId' => $clientCookie,
                'serverIp' => $request->ip(),
                'serverHost' => $request->header('tarkan-domain'),
                'username' => $me['email'],
                'userIp' => $request->header('x-real-ip'),
                'userAgent' => $request->userAgent(),
                'log' => [
                    'code' => $code,
                    'object' => $objecto,
                    'status' => $geofence->status(),
                    'old'=> $geofenceData
                ]
            ]);

            return response($geofence->body(),$geofence->status());
        }else{
            return response($me->body(),$me->status());
        }
    }


}

