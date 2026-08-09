<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class GeofenceController extends Controller{

    public function post(Request $request){
        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));
        if($me->status()!==200) {
            return response($me->body(),$me->status());
        }

        $data = $request->all();

        if (is_array($data['attributes']) && count($data['attributes'])===0) {
            $data['attributes'] = (object)null;
        }

        $attributes = $request->json('attributes');

        $geofence = $traccar->createGeofence($data, self::cookieAuth($request));

        if(isset($attributes['isAnchor'])){
            $objecto = ['deviceId'=>$attributes['deviceId']];
            $code = 405;
        }else{
            $objecto = ['geofenceId'=>$geofence->json('id')];
            $code = 401;
        }

        UserLog::record($request, $me['email'], $code, $geofence->status(), [
            'object' => $objecto,
            'data' => $data
        ]);

        return response($geofence->body(),$geofence->status());
    }

    public function delete(Request $request){
        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));
        if($me->status()!==200) {
            return response($me->body(),$me->status());
        }

        $geofenceData = $traccar->getGeofences($request->geofenceId, self::cookieAuth($request));
        $geofenceData = $geofenceData->json();

        $geofence = $traccar->deleteGeofence($request->geofenceId, self::cookieAuth($request));

        if(isset($geofenceData['attributes']['isAnchor'])){
            $objecto = ['deviceId'=>$geofenceData['attributes']['deviceId']];
            $code = 406;
        }else{
            $objecto = ['geofenceId'=>$request->geofenceId];
            $code = 402;
        }

        UserLog::record($request, $me['email'], $code, $geofence->status(), [
            'object' => $objecto,
            'old' => $geofenceData
        ]);

        return response($geofence->body(),$geofence->status());
    }


}
