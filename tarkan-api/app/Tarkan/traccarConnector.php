<?php

namespace App\Tarkan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class traccarConnector{

    private $config;

    public function __construct($request){

        if(env('TARKAN_HOST',false) && env('TARKAN_USERNAME',false) && env('TARKAN_PASSWORD',false)){
            $this->config = [
                "host" => env('TARKAN_HOST',false) . "/api",
                "username" => env('TARKAN_USERNAME',false),
                "password" => env('TARKAN_PASSWORD',false)
            ];

            return;
        }

        $remoteAddr = $request->ip();
        $remoteHost = $request->header('tarkan-domain');
        $traccarHost = $request->header('traccar-host');

        $this->config = [
            "host" => (isset($traccarHost) ? $traccarHost : "https://" . $remoteHost) . "/api",
            "username" => "",
            "password" => ""
        ];

        $configPaths = [
            'assets/default/config.json',
            'assets/' . $remoteAddr . '/config.json',
            'assets/' . $remoteAddr . '/' . $remoteHost . '/config.json',
        ];

        foreach ($configPaths as $path) {
            if (Storage::exists($path)) {
                $fileConfig = json_decode(Storage::get($path), true);

                foreach (['host', 'username', 'password'] as $key) {
                    if (isset($fileConfig[$key])) {
                        $this->config[$key] = $fileConfig[$key];
                    }
                }
            }
        }

    }

    /**
     * Executa um request HTTP contra o Traccar aplicando a estratégia de auth:
     * headers customizados (cookie de sessão) > basic auth de params > basic auth da config.
     */
    private function request($method, $url, $body = null, $params = [], $timeout = null){

        $request = Http::acceptJson();

        if($timeout){
            $request->timeout($timeout);
        }

        if(isset($params['h'])){
            $request->withHeaders($params['h']);
        }else{
            if(isset($params['username']) && isset($params['password'])){
                $request->withBasicAuth($params['username'], $params['password']);
            }else{
                $request->withBasicAuth(
                    $this->config['username'],
                    $this->config['password']
                );
            }
        }

        return $request->$method($url, $body);
    }


    public function putServer($body,$params=[]){
        return $this->request('put', $this->config['host']."/server", $body, $params, 10);
    }

    public function getServer($params=[]){
        return $this->request('get', $this->config['host']."/server", null, $params, 10);
    }

    public function getSession($params=[]){
        return $this->request('get', $this->config['host']."/session", null, $params, 10);
    }

    public function postSession($params=[]){
        $request = Http::acceptJson()->asForm()->timeout(10);

        if(isset($params['h'])){
            $request->withHeaders($params['h']);
        }

        return $request->post($this->config['host']."/session", $params);
    }

    public function getDevices($params=[]){
        return $this->request('get', $this->config['host']."/devices?all=true", null, $params);
    }

    public function getDevice($deviceId,$params=[]){
        if(is_array($deviceId)){
            $_params = [];
            foreach($deviceId as $id){
                $_params[] = "id=".$id;
            }

            $_params = implode("&",$_params);
        }else{
            $_params = "id=".$deviceId;
        }

        return $this->request('get', $this->config['host']."/devices?".$_params, null, $params);
    }

    public function getDeviceByImei($deviceId,$params=[]){
        if(is_array($deviceId)){
            $_params = [];
            foreach($deviceId as $id){
                $_params[] = "uniqueId=".$id;
            }

            $_params = implode("&",$_params);
        }else{
            $_params = "uniqueId=".$deviceId;
        }

        return $this->request('get', $this->config['host']."/devices?".$_params, null, $params);
    }

    public function createDevice($data,$params=[]){
        return $this->request('post', $this->config['host']."/devices", $data, $params);
    }

    public function saveDevice($deviceId,$data,$params=[]){
        return $this->request('put', $this->config['host']."/devices/".$deviceId, $data, $params);
    }

    public function deleteDevice($deviceId,$params=[]){
        return $this->request('delete', $this->config['host']."/devices/".$deviceId, null, $params);
    }

    public function createUser($data,$params=[]){
        return $this->request('post', $this->config['host']."/users", $data, $params);
    }

    public function updateUser($userId,$data,$params=[]){
        return $this->request('put', $this->config['host']."/users/".$userId, $data, $params);
    }

    public function deleteUser($userId,$params=[]){
        return $this->request('delete', $this->config['host']."/users/".$userId, null, $params);
    }

    public function getUsers($id=false,$params=[]){
        return $this->request('get', $this->config['host']."/users".(($id!==false)?'/'.$id:''), null, $params);
    }

    public function getAllNotifications($params=[]){
        return $this->request('get', $this->config['host']."/notifications?all=true", null, $params);
    }

    public function getNotifications($id=false,$params=[]){
        return $this->request('get', $this->config['host']."/notifications".(($id!==false)?'/'.$id:''), null, $params);
    }

    public function getAvailableCommands($deviceId,$params=[]){
        return $this->request('get', $this->config['host']."/commands/send?deviceId=".$deviceId, null, $params);
    }

    public function sendCommand($command,$params){
        return $this->request('post', $this->config['host']."/commands/send", $command, $params);
    }

    public function sendStopEngine($deviceId,$params=[]){
        return $this->request('post', $this->config['host']."/commands/send", [
            "id"=>0,
            "description"=>"Novo...",
            "deviceId"=>$deviceId,
            "type"=>"engineStop",
            "textChannel"=>false,
            "attributes"=> (object) null
        ], $params);
    }

    public function sendResumeEngine($deviceId,$params=[]){
        return $this->request('post', $this->config['host']."/commands/send", [
            "id"=>0,
            "description"=>"Novo...",
            "deviceId"=>$deviceId,
            "type"=>"engineResume",
            "textChannel"=>false,
            "attributes"=> (object) null
        ], $params);
    }

    public function createGeofence($data,$params=[]){
        return $this->request('post', $this->config['host']."/geofences", $data, $params);
    }

    public function getGeofences($geofenceId=false,$params=[]){
        return $this->request('get', $this->config['host']."/geofences".(($geofenceId)?'/'.$geofenceId:''), null, $params);
    }

    public function deleteGeofence($geofenceId=false,$params=[]){
        return $this->request('delete', $this->config['host']."/geofences".(($geofenceId)?'/'.$geofenceId:''), null, $params);
    }

    public function linkObjects($data,$params=[]){
        return $this->request('post', $this->config['host']."/permissions", $data, $params);
    }

    public function unlinkObjects($data,$params=[]){
        return $this->request('delete', $this->config['host']."/permissions", $data, $params);
    }

    public function getPermissions($qs=[],$params=[]){
        $query = count($qs) ? '?'.http_build_query($qs) : '';
        return $this->request('get', $this->config['host']."/permissions".$query, null, $params);
    }

    public function postPermissionsBulk($data,$params=[]){
        return $this->request('post', $this->config['host']."/permissions/bulk", $data, $params);
    }

    public function deletePermissionsBulk($data,$params=[]){
        return $this->request('delete', $this->config['host']."/permissions/bulk", $data, $params);
    }

    public function getRoute($qs,$params=[]){
        return $this->request('get', $this->config['host']."/reports/route?".$qs, null, $params);
    }

    public function getSummary($qs,$params=[]){
        return $this->request('get', $this->config['host']."/reports/summary?".$qs, null, $params);
    }

    public function getTrips($qs,$params=[]){
        return $this->request('get', $this->config['host']."/reports/trips?".$qs, null, $params);
    }

    public function createDriver($data,$params=[]){
        return $this->request('post', $this->config['host']."/drivers", $data, $params);
    }

    public function getDrivers($params=[]){
        return $this->request('get', $this->config['host']."/drivers", null, $params);
    }

    public function getComputed($params=[]){
        return $this->request('get', $this->config['host']."/attributes/computed", null, $params);
    }

    public function createComputed($data,$params=[]){
        return $this->request('post', $this->config['host']."/attributes/computed", $data, $params);
    }

}
