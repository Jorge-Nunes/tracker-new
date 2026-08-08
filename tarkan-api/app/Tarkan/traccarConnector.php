<?php

namespace App\Tarkan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class traccarConnector{

    /**
     * @var string|null
     */
    private $remoteAddr;
    /**
     * @var array|string|null
     */
    private $remoteHost;

    /**
     * @var string
     */
    private $globalConfig;
    /**
     * @var false
     */
    private $domainConfig;
    /**
     * @var false
     */
    private $ipConfig;

    private $config;

    private $traccarHost;

    public function __construct($request){

            if(env('TARKAN_HOST',false) && env('TARKAN_USERNAME',false) && env('TARKAN_PASSWORD',false)){
                $this->config = [
                    "host" => env('TARKAN_HOST',false) . "/api",
                    "username" => env('TARKAN_USERNAME',false),
                    "password" => env('TARKAN_PASSWORD',false)
                ];
            }else{


                $this->remoteAddr = $request->ip();
                $this->remoteHost = $request->header('tarkan-domain');
                $this->traccarHost = $request->header('traccar-host');
                $this->globalConfig = false;
                $this->domainConfig = false;
                $this->ipConfig = false;

                $this->config = [
                    "host" => ((isset($this->traccarHost)) ? $this->traccarHost : "https://" . $this->remoteHost) . "/api",
                    "username" => "",
                    "password" => ""
                ];

                if (Storage::exists('assets/default/config.json')) {
                    $this->globalConfig = json_decode(Storage::get('assets/default/config.json'), true);

                    if (isset($this->globalConfig['host'])) {
                        $this->config['host'] = $this->globalConfig['host'];
                    }
                    if (isset($this->globalConfig['username'])) {
                        $this->config['username'] = $this->globalConfig['username'];
                    }
                    if (isset($this->globalConfig['password'])) {
                        $this->config['password'] = $this->globalConfig['password'];
                    }

                }

                $domainPath = 'assets/' . $this->remoteAddr . '/' . $this->remoteHost . '/config.json';
                $ipPath = 'assets/' . $this->remoteAddr . '/config.json';

                if (Storage::exists($ipPath)) {
                    $this->ipConfig = json_decode(Storage::get($ipPath), true);


                    if (isset($this->ipConfig['host'])) {
                        $this->config['host'] = $this->ipConfig['host'];
                    }
                    if (isset($this->ipConfig['username'])) {
                        $this->config['username'] = $this->ipConfig['username'];
                    }
                    if (isset($this->ipConfig['password'])) {
                        $this->config['password'] = $this->ipConfig['password'];
                    }

                }

                if (Storage::exists($domainPath)) {
                    $this->domainConfig = json_decode(Storage::get($domainPath), true);


                    if (isset($this->domainConfig['host'])) {
                        $this->config['host'] = $this->domainConfig['host'];
                    }
                    if (isset($this->domainConfig['username'])) {
                        $this->config['username'] = $this->domainConfig['username'];
                    }
                    if (isset($this->domainConfig['password'])) {
                        $this->config['password'] = $this->domainConfig['password'];
                    }
                }
            }



    }


    public function putServer($body,$params=[]){

        $request = Http::acceptJson();
        $request->timeout(10);

        $URL = $this->config['host']."/server";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->put($URL,$body);
    }

    public function getServer($params=[]){

        $request = Http::acceptJson();
        $request->timeout(10);

        $URL = $this->config['host']."/server";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function getSession($params=[]){

        $request = Http::acceptJson();
        $request->timeout(10);

        $URL = $this->config['host']."/session";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function postSession($params=[]){


        $request = Http::acceptJson();
        $request->asForm();
        $request->timeout(10);


        $URL = $this->config['host']."/session";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }


        return $request->post($URL,$params);
    }

    public function getDevices($params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/devices?all=true";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function getDevice($deviceId,$params=[]){
        $request = Http::acceptJson();

        if(is_array($deviceId)){
            $_params = [];
            foreach($deviceId as $id){
                $_params[] = "id=".$id;
            }

            $_params = implode("&",$_params);
        }else{
            $_params = "id=".$deviceId;
        }

        $URL = $this->config['host']."/devices?".$_params;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function getDeviceByImei($deviceId,$params=[]){
        $request = Http::acceptJson();

        if(is_array($deviceId)){
            $_params = [];
            foreach($deviceId as $id){
                $_params[] = "uniqueId=".$id;
            }

            $_params = implode("&",$_params);
        }else{
            $_params = "uniqueId=".$deviceId;
        }

        $URL = $this->config['host']."/devices?".$_params;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function createDevice($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/devices";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,$data);
    }

    public function saveDevice($deviceId,$data,$params=[]){
        $request = Http::acceptJson();


        $URL = $this->config['host']."/devices/".$deviceId;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }


        return $request->put($URL,$data);
    }


    public function deleteDevice($deviceId,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/devices/".$deviceId;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->delete($URL);
    }


    public function createUser($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/users";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,$data);
    }


    public function updateUser($userId,$data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/users/".$userId;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->put($URL,$data);
    }

    public function deleteUser($userId,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/users/".$userId;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->delete($URL);
    }

    public function getUsers($id=false,$params=[]){

        $request = Http::acceptJson();

        $URL = $this->config['host']."/users".(($id!==false)?'/'.$id:'');

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }



    public function getAllNotifications($params=[]){

        $request = Http::acceptJson();

        $URL = $this->config['host']."/notifications?all=true";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }


    public function getNotifications($id=false,$params=[]){

        $request = Http::acceptJson();

        $URL = $this->config['host']."/notifications".(($id!==false)?'/'.$id:'');

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function getAvailableCommands($deviceId,$params=[]){

        $request = Http::acceptJson();

        $URL = $this->config['host']."/commands/send?deviceId=".$deviceId;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function sendCommand($command,$params){

        $request = Http::acceptJson();

        $URL = $this->config['host']."/commands/send";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,$command);

    }

    public function sendStopEngine($deviceId,$params=[]){

        $request = Http::acceptJson();

        $URL = $this->config['host']."/commands/send";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,[
            "id"=>0,
            "description"=>"Novo...",
            "deviceId"=>$deviceId,
            "type"=>"engineStop",
            "textChannel"=>false,
            "attributes"=> (object) null
        ]);

    }

    public function sendResumeEngine($deviceId,$params=[]){

        $request = Http::acceptJson();

        $URL = $this->config['host']."/commands/send";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }


        return $request->post($URL,[
            "id"=>0,
            "description"=>"Novo...",
            "deviceId"=>$deviceId,
            "type"=>"engineResume",
            "textChannel"=>false,
            "attributes"=> (object) null
        ]);

    }

    public function createGeofence($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/geofences";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,$data);
    }

    public function getGeofences($geofenceId=false,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/geofences".(($geofenceId)?'/'.$geofenceId:'');

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function deleteGeofence($geofenceId=false,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/geofences".(($geofenceId)?'/'.$geofenceId:'');

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->delete($URL);
    }

    public function linkObjects($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/permissions";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }


        return $request->post($URL,$data);
    }



    public function unlinkObjects($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/permissions";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }


        return $request->delete($URL,$data);
    }

    public function getPermissions($qs=[],$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/permissions".((count($qs))?'?'.http_build_query($qs):'');

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function postPermissionsBulk($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/permissions/bulk";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,$data);
    }

    public function deletePermissionsBulk($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/permissions/bulk";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->delete($URL,$data);
    }

    public function getRoute($qs,$params=[]){
        $request = Http::acceptJson();


        $_params = $qs;

        $URL = $this->config['host']."/reports/route?".$_params;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }



    public function getSummary($qs,$params=[]){
        $request = Http::acceptJson();


        $_params = $qs;

        $URL = $this->config['host']."/reports/summary?".$_params;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function getTrips($qs,$params=[]){
        $request = Http::acceptJson();


        $_params = $qs;

        $URL = $this->config['host']."/reports/trips?".$_params;

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }


    public function createDriver($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/drivers";

        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,$data);
    }


    public function getDrivers($params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/drivers";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }





    public function getComputed($params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/attributes/computed";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->get($URL);
    }

    public function createComputed($data,$params=[]){
        $request = Http::acceptJson();

        $URL = $this->config['host']."/attributes/computed";


        if(IsSet($params['h'])){
            $request->withHeaders($params['h']);
        }else if(IsSet($params['username']) && IsSet($params['password'])){
            $request->withBasicAuth($params['username'],$params['password']);
        }else{
            $request->withBasicAuth(
                $this->config['username'],
                $this->config['password']
            );
        }

        return $request->post($URL,$data);
    }

}
