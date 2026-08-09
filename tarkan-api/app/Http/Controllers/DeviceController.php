<?php


namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DeviceController extends Controller{



    public function autoLink(Request $request){

        $data = $request->all();

        $traccar = self::traccar($request);

        $me = $traccar->getSession(self::cookieAuth($request));
        if($me->status()!==200) {
            return response('User not authed', 503);
        }

        $userData = $me->json();

        $device = $traccar->getDeviceByImei($data['imei']);
        if($device->status()===200 && count($device->json())>0){
            $devices = $device->json();
            $device = $devices[0];

            $device['name'] = $userData['name'].' - '.$data['placa'];
            $device['model'] = $data['modelo'];
            $device['category'] = $data['categoria'];
            $device['attributes']['placa'] = $data['placa'];


            $traccar->saveDevice($device['id'],$device);

            $traccar->linkObjects(['userId'=>$userData['id'],'deviceId'=>$device['id']]);


            return response('OK',200);
        }

        return response('Invalid device', 503);

    }

    public function post(Request $request){
        $data = $request->all();

        if (is_array($data) && isset($data['attributes']) && is_array($data['attributes']) && count($data['attributes']) == 0) {
            $data['attributes'] = (object)null;
        }

        if (is_array($data) && isset($data['geofenceIds'])) {
            unset($data['geofenceIds']);
        }

        $traccar = self::traccar($request);

        $server = $traccar->getServer();

        $svJSON = $server->json();
        $devices = $traccar->getDevices();

        $serverLimit = (isset($svJSON['attributes']['tarkan.deviceLimit']) ? $svJSON['attributes']['tarkan.deviceLimit'] : null);

        if($serverLimit !== null && $serverLimit >= 0){
            $deviceLimit = $serverLimit - count($devices->json());
            if(($deviceLimit-1)<=0){
                return response('Server Device Limit Exceeded', 503);
            }
        }

        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $response = $traccar->createDevice($data, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 301, $response->status(), [
            'object' => ['deviceId' => (isset($data['id'])) ? intval($data['id']) : 0],
            'old' => false,
            'data' => $data
        ]);

        return response($response->body(), $response->status());
    }


    public function put(Request $request){
        $data = $request->json()->all();

        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $saveData = $request->all();
        if (isset($saveData['attributes']) && is_array($saveData['attributes']) && count($saveData['attributes']) == 0) {
            $saveData['attributes'] = (object)null;
        }
        if (isset($saveData['geofenceIds'])) {
            unset($saveData['geofenceIds']);
        }

        $old = $traccar->getDevice($request->deviceId, self::cookieAuth($request));

        $response = $traccar->saveDevice($request->deviceId, $saveData, self::cookieAuth($request));

        UserLog::record($request, $me['email'], 301, $response->status(), [
            'object' => ['deviceId' => (isset($request->deviceId)) ? intval($request->deviceId) : 0],
            'old' => $old->json(),
            'data' => $data
        ]);

        return response($response->body(),$response->status());
    }

    public function delete(Request $request){
        $auth = self::authedTraccar($request);
        if($auth === false){
            return response('User not authed', 503);
        }

        [$traccar, $me] = $auth;

        $server = $traccar->getServer()->json();

        $old = $traccar->getDevice($request->deviceId, self::cookieAuth($request));

        if($server['attributes'] && isset($server['attributes']['tarkan.enableLazyDeletion']) && $server['attributes']['tarkan.enableLazyDeletion']===true){

            $tmp = $old->json();
            $tmp = $tmp[0];

            if (isset($tmp['attributes']) && is_array($tmp['attributes']) && count($tmp['attributes'])==0) {
                $tmp['attributes'] = (object)null;
            }

            $tmp['uniqueId'] = 'deleted-'.$tmp['uniqueId'].'-'.time();
            $response = $traccar->saveDevice($request->deviceId, $tmp);
        }else {
            $response = $traccar->deleteDevice($request->deviceId, self::cookieAuth($request));
        }

        UserLog::record($request, $me['email'], 302, $response->status(), [
            'object' => ['deviceId' => (isset($request->deviceId)) ? intval($request->deviceId) : 0],
            'old' => $old->json()
        ]);

        return response($response->body(),$response->status());
    }

    public function uploadImage(Request $request){

        if(!Storage::exists('assets/'.$request->ip().'/assets/images/')){
            Storage::makeDirectory('assets/'.$request->ip().'/assets/images/');
        }


        Image::make($request->file('image'))->fit('170', '140')->save(storage_path(). '/app/assets/'.$request->ip().'/assets/images/' . $request->deviceId .'.jpg')->encode('jpg', 80);

        return response()->json(['img'=>true]);
    }


    public static function stopEngine(Request $request,$deviceId){

        $traccar = self::traccar($request);

        $changeNative = false;

        $availableCommands = $traccar->getAvailableCommands($deviceId);
        if($availableCommands->status()===200){
            $commands = $availableCommands->json();

            foreach($commands as $command){
                if($command['attributes']['tarkan.changeNative']==='engineStop'){
                    $changeNative = $command;
                    break;
                }
            }
        }

        if($changeNative){
            $command['deviceId'] = $deviceId;
            $send = $traccar->sendCommand($command);
        }else {
            $send = $traccar->sendStopEngine($deviceId);
        }

        return ['status'=>$send->status(),'body'=>$send->body(),'native'=>$changeNative];
    }

    public static function resumeEngine(Request $request,$deviceId){

        $traccar = self::traccar($request);

        $changeNative = false;

        $availableCommands = $traccar->getAvailableCommands($deviceId);
        if($availableCommands->status()===200){
            $commands = $availableCommands->json();

            foreach($commands as $command){
                if($command['attributes']['tarkan.changeNative']==='engineResume'){
                    $changeNative = $command;
                    break;
                }
            }
        }

        if($changeNative){
            $command['deviceId'] = $deviceId;
            $send = $traccar->sendCommand($command);
        }else {
            $send = $traccar->sendResumeEngine($deviceId);
        }

        return ['status'=>$send->status(),'body'=>$send->body(),'native'=>$changeNative];
    }
}
